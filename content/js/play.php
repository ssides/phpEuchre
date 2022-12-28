<?php 
  include_once('../../config/config.php'); 
?>

<script type="text/javascript">
  
  // The gameController is responsible for calling on viewmodels to update the score, and do animations on 
  // the play table.  The main interval timer is the getGameInterval which runs throughout the game.
  // The computer used by the Organizer does the scoring of the game.  So if the Organizer's Partner goes
  // alone, the Organizer's gameController must continue to be active. (I say that because when my iPhone
  // goes to sleep, javascript processes stop running.)
  function gameController() {
    var self = this;
    
    self.game = new gameModel({});
    self.executionPoint = 0;
    self.position = null;
    self.playerID = '<?php echo "{$_COOKIE[$cookieName]}"; ?>';
    self.dealID = null;
    self.bidModal = new bootstrap.Modal($('#bidModal'));
    self.endGameModal = new bootstrap.Modal($('#endGameModal'));
    self.whatsTrumpVM = new whatsTrumpViewModel();
    self.nPlayerInfoVM = new playerInfoViewModel();
    self.ePlayerInfoVM = new playerInfoViewModel();
    self.wPlayerInfoVM = new playerInfoViewModel();
    self.playerInfoVM = new currentPlayerInfoViewModel();
    self.myScoreVM = new scoreViewModel();
    self.opponentScoreVM = new scoreViewModel();
    self.playVM = new playViewModel();
    self.bidDialogVM = new bidDialogViewModel();
    self.endGameDialogVM = new endGameDialogViewModel();
    self.getGameInterval = null;
    self.orgGetNextFInterval = null;
    self.playerGetCurrentFInterval = null;
    self.firstDealer = ' ';
    self.previousPO = '';
    self.previousPP = '';
    self.previousPL = '';
    self.previousPR = '';
    
    self.setThisPlayerPosition = function(gameData){
      if ((self.playerID === gameData.Organizer)) {
        self.position = 'O';
      } else if (self.playerID === gameData.Left) {
        self.position = 'L';
      } else if (self.playerID === gameData.Right) {
        self.position = 'R';
      } else if (self.playerID === gameData.Partner) {
        self.position = 'P';
      } else {
        self.position = ' ';
      }
    };

    self.getExecutionPointIndex = function(id) {
      for(var i = 0; i < self.gameExecution.length; i++) {
        if (self.gameExecution[i].id === id) {
          return i;
        }
      }
      return 0;
    };
    
    self.setExecutionPoint = function(id) {
      console.log('execution point: ' + id);
      self.logGameControllerState(id, null);
      self.executionPoint = self.getExecutionPointIndex(id);
    };
    
    self.updateScoresAndInfo = function() {
      self.whatsTrumpVM.update(self.game);
      self.nPlayerInfoVM.update(self.game);
      self.ePlayerInfoVM.update(self.game);
      self.wPlayerInfoVM.update(self.game);
      self.playerInfoVM.update(self.game, self.dealID);
      self.myScoreVM.update(self.game);
      self.opponentScoreVM.update(self.game);
      if (self.game.OpponentTrump || self.game.OrganizerTrump) {
        $('#cardFaceUp').hide();
      } else if (self.game.CardFaceUp.length == 2) {
        self.playVM.faceupCardURL(app.getCardURL(self.game.CardFaceUp.substr(0,2)));
        $('#cardFaceUp').show();
      } else if (self.game.CardFaceUp.length > 3 && self.game.CardFaceUp[2] == 'U' || self.game.CardFaceUp[2] == 'D') {
        self.playVM.faceupCardURL(app.getCardURL('cardback'));
        $('#cardFaceUp').show();
      } else {
        $('#cardFaceUp').hide();
      } 
    };

    self.acknowledge = function(position) {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.position = position;
      console.log('acknowledge()');
      $.ajax({
        method: 'POST',
        url: 'api/acknowledge.php',
        data: pd,
        success: function (response) {
          console.log('acknowledge response', response);
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };

    self.acknowledgeCard = function(playerID) {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.position;
      pd.playerID = playerID;
            console.log('acknowledgeCard()');

      $.ajax({
        method: 'POST',
        url: 'api/acknowledgeCard.php',
        data: pd,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) 
              console.log(data.ErrorMsg);
          } catch(error) {
            console.log('Could not parse response from acknowledgeCard. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };
    
    self.logHand = function(newScore) {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.dealID = self.dealID;
      pd.lead = self.game.Lead;
      pd.cardO = self.game.PO;
      pd.cardP = self.game.PP;
      pd.cardL = self.game.PL;
      pd.cardR = self.game.PR;
      pd.organizerTrump = self.game.OrganizerTrump || '-';
      pd.opponentTrump = self.game.OpponentTrump || '-';
      pd.organizerScore = newScore.organizerScore;
      pd.organizerTricks = newScore.organizerTricks;
      pd.opponentScore = newScore.opponentScore;
      pd.opponentTricks = newScore.opponentTricks;
      pd.alone = self.game.CardFaceUp.length == 5 ? 'A' : '-';
      
      console.log('logHand()');

      $.ajax({
        method: 'POST',
        url: 'api/logHand.php',
        data: pd,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) 
              console.log(data.ErrorMsg);
          } catch(error) {
            console.log('Could not parse response from logHand. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };

    self.iamTheSkippedPlayer = function(){
      if (self.game.CardFaceUp.length < 5) {
        return false;
      } else {
        switch (self.game.CardFaceUp[3]) {
          case 'O':
            return self.position == 'P';
          case 'P':
            return self.position == 'O';
          case 'L':
            return self.position == 'R';
          case 'R':
            return self.position == 'L';
        }
      }
    };
    
    self.placeCardWithAcknowledge = function(card, playerID) {
      self.placeCard[app.positions.indexOf(self.position)](card, playerID);
      if (self.position != playerID && !self.iamTheSkippedPlayer())
        self.acknowledgeCard(playerID);
    };
    
    self.getCurrentStartCard = function(){
      console.log('getCurrentStartCard()');

      $.ajax({
        method: 'POST',
        url: 'api/getCurrentStartCard.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ID) {
              var i = app.positions.indexOf(self.position);
              self.placeCard[i](data.ID, data.Position);
              if (data.ID[0] == 'J') {
                self.acknowledge(self.position);
                self.setExecutionPoint('waitForAcknowledgments');
                clearInterval(self.playerGetCurrentFInterval);
              }
            } else {
              console.log(data.ErrorMsg);
            }
          } catch (error) {
            console.log('Could not parse response from getCurrentStartCard. ' + error + ': ' + response);
            clearInterval(self.playerGetCurrentFInterval);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.playerGetCurrentFInterval);
        }
      });
    };
    
    self.getNextStartCard = function() {
      console.log('getNextStartCard()');

      $.ajax({
        method: 'POST',
        url: 'api/getNextStartCard.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            var i = app.positions.indexOf(self.position);
            self.placeCard[i](data.ID, data.Position);
            if (data.ID[0] == 'J') {
              self.firstDealer = data.Position;
              self.setExecutionPoint('waitForAcknowledgments');
              clearInterval(self.orgGetNextFInterval);
            }
          } catch (error) {
            console.log('Could not parse response from getNextStartCard. ' + error + ': ' + response);
            clearInterval(self.orgGetNextFInterval);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.orgGetNextFInterval);
        }
      });
    };

    // Turn and Dealer have been set in self.game.  self.position is the dealer. 
    // The deal api selects a random deal where `PurposeCode` = 'D' and DealID has not yet been used in this game.
    // It distributes cards to players in table `Play`.
    self.deal = function() {
      console.log('deal()');

      $.ajax({
        method: 'POST',
        url: 'api/deal.php',
        data: app.apiPostData,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) 
              console.log(data.ErrorMsg);
            self.dealID = data.DealID;
            self.setExecutionPoint('waitForCardFaceUp');
          } catch (error) {
            console.log('Could not parse response from deal. ' + error + ': ' + response);
            clearInterval(self.getGameInterval);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.getGameInterval);
        }
      });
    };

    self.setDealPosition = function(position, isFirst){
      console.log('setDealPosition()');

      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.position = position;
      if (isFirst) {
        pd.isFirst = 1;
      }
      
      $.ajax({
        method: 'POST',
        url: 'api/setDealPosition.php',
        data: pd,
        success: function(response){
          // wait for the dealer to deal.
          self.setExecutionPoint('dealOrWaitForCardFaceUp');
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.getGameInterval);
        }
      });
    };

    self.updateScoreAfterHand = function (winner, newScore){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.winner = winner;
      pd.opponentTricks = newScore.opponentTricks;
      pd.opponentScore = newScore.opponentScore;
      pd.organizerTricks = newScore.organizerTricks;
      pd.organizerScore = newScore.organizerScore;
      
      return $.ajax({
        method: 'POST',
        url: 'api/updateScoreAfterHand.php',
        data: pd,
        success: function(response){
          let data = JSON.parse(response);
          if (data.ErrorMsg) {
            console.log(data.ErrorMsg);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.getGameInterval);
        }
      });
    };
    
    self.placeCard = [
      function(cardID, atPosition) { 
        // todo: use a switch statement here, and in the rest of these functions.
        if (atPosition == 'O') 
          self.playVM.sCardURL(app.getCardURL(cardID));
        else if (atPosition == 'P') 
          self.playVM.nCardURL(app.getCardURL(cardID));
        else if (atPosition == 'L') 
          self.playVM.wCardURL(app.getCardURL(cardID));
        else
          self.playVM.eCardURL(app.getCardURL(cardID));
      },
      function(cardID, atPosition) { 
        if (atPosition == 'O') 
          self.playVM.nCardURL(app.getCardURL(cardID));
        else if (atPosition == 'P') 
          self.playVM.sCardURL(app.getCardURL(cardID));
        else if (atPosition == 'L') 
          self.playVM.eCardURL(app.getCardURL(cardID));
        else
          self.playVM.wCardURL(app.getCardURL(cardID));
      },
      function(cardID, atPosition) { 
        if (atPosition == 'O') 
          self.playVM.eCardURL(app.getCardURL(cardID));
        else if (atPosition == 'P') 
          self.playVM.wCardURL(app.getCardURL(cardID));
        else if (atPosition == 'L') 
          self.playVM.sCardURL(app.getCardURL(cardID));
        else
          self.playVM.nCardURL(app.getCardURL(cardID));
      },
      function(cardID, atPosition) { 
        if (atPosition == 'O') 
          self.playVM.wCardURL(app.getCardURL(cardID));
        else if (atPosition == 'P') 
          self.playVM.eCardURL(app.getCardURL(cardID));
        else if (atPosition == 'L') 
          self.playVM.nCardURL(app.getCardURL(cardID));
        else
          self.playVM.sCardURL(app.getCardURL(cardID));
      }
    ];

    self.clearTable = function(){
      self.playVM.nCardURL('');  self.playVM.eCardURL('');  self.playVM.sCardURL('');  self.playVM.wCardURL('');
    };

    self.getGame = function() {
      $.ajax({
        method: 'POST',
        url: 'api/getGame.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            } else {
              self.game = new gameModel(data.Game);
              self.updateScoresAndInfo();
              self.gameExecution[self.executionPoint].fn();
            }
          } catch (error) {
            console.log('Error ' + ': ' + error.message || error);
            console.log(error.stack);
            clearInterval(self.getGameInterval);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.getGameInterval);
        }
      });
    };
    
    self.endGame = function(){
            console.log('endGame()');

      $.ajax({
        method: 'POST',
        url: 'api/endGame.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            var data = JSON.parse(response);
            if (data.ErrorMsg) 
              console.log(data.ErrorMsg);
          } catch (error) {
            console.log('Error ' + ': ' + error.message || error);
            console.log(error.stack);
            clearInterval(self.getGameInterval);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.getGameInterval);
        }
      });
    };
    
    self.getDealID = function() {
      console.log('getDealID()');
      return $.ajax({
        method: 'POST',
        url: 'api/getDealID.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            var data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            }
            self.dealID = data.DealID;
          } catch (error) {
            console.log('Error ' + ': ' + error.message || error);
            console.log(error.stack);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };

    self.scoringFinished = function() {
      console.log('scoringFinished()');
      $.ajax({
        method: 'POST',
        url: 'api/scoringFinished.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            var data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            }
          } catch (error) {
            console.log('Error ' + ': ' + error.message || error);
            console.log(error.stack);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };

    self.logGameControllerState = function(state,message) {
      console.log('logGameControllerState()');
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.dealID = self.dealID;
      pd.positionID = self.position;
      pd.state = state;
      pd.message = message || '';
      pd.organizerScore = self.game.OrganizerScore;
      pd.opponentScore = self.game.OpponentScore;
      pd.organizerTricks = self.game.OrganizerTricks;
      pd.opponentTricks = self.game.OpponentTricks;
      
      $.ajax({
        method: 'POST',
        url: 'api/logGameControllerState.php',
        data: pd,
        success: function (response) {
          try {
            var data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            }
          } catch (error) {
            console.log('Error ' + ': ' + error.message || error);
            console.log(error.stack);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };


    self.initializeFn = function(){
      self.setThisPlayerPosition(self.game);
      self.nPlayerInfoVM.initialize('N', self.position, self.game);
      self.ePlayerInfoVM.initialize('E', self.position, self.game);
      self.wPlayerInfoVM.initialize('W', self.position, self.game);
      self.playerInfoVM.initialize(self.position, self.game);
      self.myScoreVM.initialize('M', self.position, self.game);
      self.opponentScoreVM.initialize('O', self.position, self.game);
      
      if (self.game.Dealer) {
        if (!self.dealID) {
          // If the player is returning to a game, there will be a dealer, but no dealID.
          $.when(self.getDealID()).done(function(){
            // If they closed the browser during scoring of a hand, need a new deal.
            if (!self.dealID) {
              self.setExecutionPoint('dealOrWaitForCardFaceUp');
            }
          });
        }
        if (self.game.allCardsHaveBeenPlayed()) {
          if (self.position == 'O') {
            self.setExecutionPoint('scoreHand');
          } else {
            self.setExecutionPoint('clearTable');
          }
        } else {
          self.setExecutionPoint('waitForTrump');
        }
      } else if (self.game.GameFinishDate) {
        self.setExecutionPoint('showGameOverDialog');
      } else {
        self.setExecutionPoint('selectFirstJack');
      }
    };
    
    self.selectFirstJackFn = function(){
      if (!self.position)
        throw "self.position is null";
      
      if (self.game.Dealer)
        throw "selectFirstJackFn(): dealer is already set.";
      
      if ((self.position == 'O') && (self.orgGetNextFInterval === null)) {
        console.log('Starting the first Jack selection timer.');
        self.orgGetNextFInterval = setInterval(self.getNextStartCard, app.times.firstJackTime);
      }
      
      if ((self.position != 'O') && (self.playerGetCurrentFInterval === null)) {
        console.log('Starting the first Jack query timer.');
        self.playerGetCurrentFInterval = setInterval(self.getCurrentStartCard, app.times.firstJackTime);
      }
    };
    
    self.idleFn = function() {
    };
    
    self.dealOrWaitForCardFaceUpFn = function() {
      if (self.position === self.game.Dealer) {
        self.deal();
      } else {
        self.setExecutionPoint('waitForCardFaceUp');
      }
    }
    
    self.waitForAcknowledgmentsFn = function(){
      if (self.game.allPlayersHaveAcknowledged()) {
        // all the players have acknowledged seeing the first Jack.
        self.clearTable();
        if (self.position == 'O') {
          self.setDealPosition(self.firstDealer, 'isFirst');
        } else {
          self.setExecutionPoint('dealOrWaitForCardFaceUp');
        }
      }
    };

    self.waitForCardFaceUpFn = function(){
      if (self.game.CardFaceUp[0] != ' ') {
        if (!self.dealID) {
          // If I'm not the dealer, I need to get the dealID.
          self.getDealID();
        }
        self.setExecutionPoint('waitForTrump');
      }
    };
    
    self.waitForTrumpFn = function() {
      // The getGameInterval timer is running and will update the page.
      // The currentPlayerInfoViewModel.update() method is called on 
      // every heartbeat.  That VM needs to be smart enough to work
      // efficiently based on the game state determined by several
      // fields in gameModel.
      
      var trump = self.game.OrganizerTrump || self.game.OpponentTrump;
      
      if (!trump && self.game.CardFaceUp.length > 2 && self.game.CardFaceUp[2] == 'D' && self.game.Turn == self.position) {
        self.bidDialogVM.update(self.position, self.game);
        self.bidModal.show();
        self.playerInfoVM.bidModalShown(true);
        self.bidDialogVM.isActive(true);
        self.setExecutionPoint('waitForBidDialog');
      }
      if (!trump && self.game.CardFaceUp.length > 2 && self.game.CardFaceUp[2] == 'U') {
        self.setExecutionPoint('waitForDiscard');
      }
      if (trump) {
        self.setExecutionPoint('waitForPlay');
      }
    };
    
    self.waitForBidDialogFn = function(){
      if (self.bidDialogVM.submitted) {
        self.bidModal.hide();
        self.playerInfoVM.bidModalShown(false);
        self.bidDialogVM.isActive(false);
        self.setExecutionPoint('waitForTrump');
      }
    };
    
    self.waitForPlayFn = function(){
      // whenever a card is played, show it on the screen.
      // wait for acknowledgments. when 4 cards (3 in alone mode) are played,
      // log the hand, set turn, clear lead.  Call into playerInfoVM
      // for that so that all the rules are in one place.
      
      if (self.previousPO != self.game.PO) {
        self.previousPO = self.game.PO;
        self.placeCardWithAcknowledge(self.game.PO, 'O');
      }
      if (self.previousPP != self.game.PP) {
        self.previousPP = self.game.PP;
        self.placeCardWithAcknowledge(self.game.PP, 'P');
      }
      if (self.previousPL != self.game.PL) {
        self.previousPL = self.game.PL;
        self.placeCardWithAcknowledge(self.game.PL, 'L');
      }
      if (self.previousPR != self.game.PR) {
        self.previousPR = self.game.PR;
        self.placeCardWithAcknowledge(self.game.PR, 'R');
      }
      
      if (self.game.allCardsHaveBeenPlayed()) {
        if (self.position == 'O') {
          self.setExecutionPoint('scoreHand');
        } else {
          self.setExecutionPoint('clearTableAsPlayer');
        }
      }
    };
    
    // The user (organizer) is reentering the game, and all cards have been played.
    self.scoreHandFn = function() {
      var winner = self.playerInfoVM.getWinnerOfHand();
      var newScore = self.playerInfoVM.getNewScore(winner);
      self.logHand(newScore);
      self.updateScoreAfterHand(winner, newScore); // sets ScoringInProgress to '1'; self.game.allCardsHaveBeenPlayed() will now be false.
      self.setExecutionPoint('clearTableAsOrganizer');
    }
    
    self.clearTableAsPlayerFn = function() {
      self.clearTable();
      self.previousPO = ''; self.previousPP = ''; self.previousPL = ''; self.previousPR = '';

      if (self.game.ScoringInProgress) {
        self.acknowledge(self.position);
        self.setExecutionPoint('waitForScore');
      }
    };
    
    self.clearTableAsOrganizerFn = function() {
      self.clearTable();
      self.previousPO = ''; self.previousPP = ''; self.previousPL = ''; self.previousPR = '';
      
      if (self.game.allPlayersHaveAcknowledged()) {
        self.scoringFinished();
        self.setExecutionPoint('waitForScore');
      }
    };
    
    self.waitForScoreFn = function(){
      if (!self.game.ScoringInProgress) {
        if (self.game.OpponentTricks == 0 && self.game.OrganizerTricks == 0) {
          var oppScore = parseInt(self.game.OpponentScore);
          var orgScore = parseInt(self.game.OrganizerScore);
          var playTo = parseInt(self.game.PlayTo);
          
          if (oppScore >= playTo || orgScore >= playTo) {
            self.endGame();
            self.setExecutionPoint('showGameOverDialog');
          } else {
            self.setExecutionPoint('dealOrWaitForCardFaceUp');
          }
        } else {
          self.setExecutionPoint('waitForPlay');
        }
      }
    };
    
    self.waitForDiscardFn = function(){
      if (self.game.CardFaceUp.length > 3 && self.game.CardFaceUp[2] == 'S') {
          self.setExecutionPoint('waitForPlay');
      }
    };
    
    self.showGameOverDialogFn = function(){
      self.endGameDialogVM.update();
      self.endGameModal.show();
      clearInterval(self.getGameInterval);
    };
    
    self.gameExecution = [
      { id: 'initialize', fn: self.initializeFn },
      { id: 'selectFirstJack', fn: self.selectFirstJackFn },
      { id: 'waitForAcknowledgments', fn: self.waitForAcknowledgmentsFn },
      { id: 'idle', fn: self.idleFn },
      { id: 'dealOrWaitForCardFaceUp', fn: self.dealOrWaitForCardFaceUpFn },
      { id: 'waitForCardFaceUp', fn: self.waitForCardFaceUpFn },
      { id: 'waitForTrump', fn: self.waitForTrumpFn },
      { id: 'waitForBidDialog', fn: self.waitForBidDialogFn },
      { id: 'waitForPlay', fn: self.waitForPlayFn },
      { id: 'scoreHand', fn: self.scoreHandFn },
      { id: 'waitForScore', fn: self.waitForScoreFn },
      { id: 'clearTableAsOrganizer', fn: self.clearTableAsOrganizerFn },
      { id: 'waitForDiscard', fn: self.waitForDiscardFn },
      { id: 'showGameOverDialog', fn: self.showGameOverDialogFn },
      { id: 'clearTableAsPlayer', fn: self.clearTableAsPlayerFn },
    ];
    
    self.initialize = function() {
      self.setExecutionPoint('initialize');
      self.getGameInterval = setInterval(self.getGame, app.times.gameTime);
    }
    
    self.initialize();

  }
  
  $(function () {
    var gc = new gameController();
    ko.applyBindings(gc.whatsTrumpVM, $('#WhatsTrump')[0]);
    ko.applyBindings(gc.nPlayerInfoVM, $('#NorthInfo')[0]);
    ko.applyBindings(gc.ePlayerInfoVM, $('#EastInfo')[0]);
    ko.applyBindings(gc.wPlayerInfoVM, $('#WestInfo')[0]);
    ko.applyBindings(gc.myScoreVM, $('#MyScore')[0]);
    ko.applyBindings(gc.opponentScoreVM, $('#OpponentScore')[0]);
    ko.applyBindings(gc.playVM, $('#PlayTable')[0]);
    ko.applyBindings(gc.playerInfoVM, $('#SouthInfo')[0]);
    ko.applyBindings(gc.bidDialogVM, $('#bidModal')[0]);
    ko.applyBindings(gc.endGameDialogVM, $('#endGameModal')[0]);
  });
  
</script>
