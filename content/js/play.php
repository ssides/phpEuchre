<?php 
  include_once('../../config/config.php'); 
?>

<script type="text/javascript">
  
  function timerOp() {
    var self = this;

    self.isStarted = false;
    self.succeeded = false;
    self.expirationTime = null;
    self.fn = null;
    
    self.start = function(expirationTime) {
      self.isStarted = true;
      self.succeeded = false;
      self.expirationTime = expirationTime;
    }
  }

  // The gameController is responsible for calling on viewmodels to update the score, and do animations on 
  // the play table.  The main loop in initialize() runs throughout the game.
  // The computer used by the Organizer does the scoring of the game.  So if the Organizer's Partner goes
  // alone, the Organizer's gameController must continue to be active. (I say that because when my iPhone
  // goes to sleep, javascript processes stop running.)
  function gameController() {
    var self = this;
    
    self.game = new gameModel({});
    self.executionPoint = 0;
    self.position = null;
    self.playerID = '<?php echo "{$$a['r']}"; ?>';
    self.bidModal = new bootstrap.Modal($('#bidModal'));
    self.finishGameModal = new bootstrap.Modal($('#finishGameModal'));
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
    self.finishGameDialogVM = new finishGameDialogViewModel();
    self.heartbeatTimer = null;
    self.heartbeatOp = new timerOp();
    self.firstjackTimer = null;  // first jack operations run at a faster rate than the heartbeat stuff.
    self.firstjackOp = new timerOp();
    self.firstDealer = ' ';
    self.delay = 0;
    self.ackSent = '';
    
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
      if (app.gameControllerLog) {
        self.logGameControllerState(id, null);
      } else {
        console.log('execution point: ' + id);
      }
      self.executionPoint = self.getExecutionPointIndex(id);
    };
    
    self.updateScoresAndInfo = function() {
      if (self.delay == 0) {
        self.whatsTrumpVM.update(self.game);
        self.nPlayerInfoVM.update(self.game);
        self.ePlayerInfoVM.update(self.game);
        self.wPlayerInfoVM.update(self.game);
        self.playerInfoVM.update(self.game);
        self.myScoreVM.update(self.game);
        self.opponentScoreVM.update(self.game);
      }
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
      // console.log('acknowledge()');
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.position = position;
      $.ajax({
        method: 'POST',
        url: 'api/acknowledge.php',
        data: pd,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            }
          } catch(error) {
            app.errorVM.add('Could not parse response from acknowledge. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
        }
      });
    };

    self.acknowledgeCard = function(playerID) {
      self.acknowledgmentSent(playerID);
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.position;
      pd.playerID = playerID;

      $.ajax({
        method: 'POST',
        url: 'api/acknowledgeCard.php',
        data: pd,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            }
          } catch(error) {
            app.errorVM.add('Could not parse response from acknowledgeCard. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
        }
      });
    };
    
    self.logHand = function(newScore) {
      // console.log('logHand()');
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.dealID = self.game.DealID;
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
      pd.cardFaceUp = self.game.CardFaceUp;
      pd.dealer = self.game.Dealer;

      $.ajax({
        method: 'POST',
        url: 'api/logHand.php',
        data: pd,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) 
              app.errorVM.add(data.ErrorMsg);
          } catch(error) {
            app.errorVM.add('Could not parse response from logHand. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
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
    
    self.acknowledgmentHasBeenSent = function(playerID){
      return self.ackSent.includes(playerID);
    };
    
    self.acknowledgmentSent = function(playerID){
      self.ackSent += playerID;
    };
    
    self.clearAcknowledgments = function(){
      self.ackSent = '';
    };
    
    self.acknowledgmentAccepted = function(playerID){
      switch (self.position) {
        case 'O':
          return self.game.ACO.includes(playerID);
        case 'P':
          return self.game.ACP.includes(playerID);
        case 'L':
          return self.game.ACL.includes(playerID);
        case 'R':
          return self.game.ACR.includes(playerID);
        default:
          return false;
      }
    };

    self.placeCardWithAcknowledge = function(card, playerID){
      if (card) {
        self.placeCard[app.positions.indexOf(self.position)](card, playerID);
        if (self.position != playerID && !self.iamTheSkippedPlayer()) {
          if (!self.acknowledgmentAccepted(playerID) && !self.acknowledgmentHasBeenSent(playerID)) {
            self.acknowledgeCard(playerID);
          }
        }
      }
    };
    
    self.getCurrentStartCard = function(){
      // console.log('getCurrentStartCard()');
      $.ajax({
        method: 'POST',
        url: 'api/getCurrentStartCard.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            self.firstjackOp.succeeded = true;
            if (data.ID) {
              var i = app.positions.indexOf(self.position);
              self.placeCard[i](data.ID, data.Position);
              if (data.ID[0] == 'J') {
                self.acknowledge(self.position);
                self.setExecutionPoint('waitForAcknowledgments');
                clearInterval(self.firstjackTimer);
              }
            } else if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            }
          } catch (error) {
            app.errorVM.add('Could not parse response from getCurrentStartCard. ' + error + ': ' + response + ': Game stopped.');
            clearInterval(self.firstjackTimer);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText + ': Game stopped.');
          clearInterval(self.firstjackTimer);
        }
      });
    };
    
    self.getNextStartCard = function(){
      // console.log('getNextStartCard()');
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
              clearInterval(self.firstjackTimer);
            } else {
              self.firstjackOp.succeeded = true;
            }
          } catch (error) {
            app.errorVM.add('Could not parse response from getNextStartCard. ' + error + ': ' + response);
            clearInterval(self.firstjackTimer);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
          clearInterval(self.firstjackTimer);
        }
      });
    };

    // Turn and Dealer have been set in self.game.  self.position is the dealer. 
    // The deal api selects a random deal where `PurposeCode` = 'D' and DealID has not yet been used in this game.
    // It distributes cards to players in table `Play`. The dealer calls self.deal().
    self.deal = function(){
      // console.log('deal()');

      $.ajax({
        method: 'POST',
        url: 'api/deal.php',
        data: app.apiPostData,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            } 
          } catch (error) {
            app.errorVM.add('Could not parse response from deal. ' + error + ': ' + response + ': Game stopped.');
            clearInterval(self.heartbeatTimer);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText + ': Game stopped.');
          clearInterval(self.heartbeatTimer);
        }
      });
    };

    self.setDealPosition = function(position, isFirst){
      // console.log('setDealPosition()');
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
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            } 
          } catch (error) {
            app.errorVM.add('Could not parse response from setDealPosition. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText + ': Game stopped.');
          clearInterval(self.heartbeatTimer);
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
            app.errorVM.add(data.ErrorMsg);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText + ': Game stopped.');
          clearInterval(self.heartbeatTimer);
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

    self.getGame = function(){
      $.ajax({
        method: 'POST',
        url: 'api/getGame.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            } else {
              self.game = new gameModel(data.Game);
              if (self.game.GameEndDate) {
                self.endGame();
              } else {
                self.updateScoresAndInfo();
                self.gameExecution[self.executionPoint].fn();
                self.heartbeatOp.succeeded = true;
              }
            }
          } catch (error) {
            app.errorVM.add('Error in getGame(): ' + error.message || error + ': Game stopped.');
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          app.errorVM.add(xhr.responseText);
        }
      });
    };
    
    self.finishGame = function(){
      // console.log('finishGame()');
      $.ajax({
        method: 'POST',
        url: 'api/finishGame.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            var data = JSON.parse(response);
            if (data.ErrorMsg) 
              app.errorVM.add(data.ErrorMsg);
          } catch (error) {
            app.errorVM.add('Error ' + ': ' + error.message || error + ': Game stopped.');
            console.log(error.stack);
            clearInterval(self.heartbeatTimer);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText + ': Game stopped.');
          clearInterval(self.heartbeatTimer);
        }
      });
    };
    
    self.endGame = function() {
      if (self.heartbeatTimer) {
        clearInterval(self.heartbeatTimer);
      }
      if (self.firstjackTimer) {
        clearInterval(self.firstjackTimer);
      }
      self.endGameModal.show();
    }
    
    self.scoringFinished = function(){
      // console.log('scoringFinished()');
      $.ajax({
        method: 'POST',
        url: 'api/scoringFinished.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            var data = JSON.parse(response);
            if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            }
          } catch (error) {
            app.errorVM.add('Error ' + ': ' + error.message || error);
            console.log(error.stack);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
        }
      });
    };

    self.logGameControllerState = function(state, message) {
      // console.log('logGameControllerState()');
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.dealID = self.game.DealID;
      pd.positionID = self.position;
      pd.state = state;
      pd.message = message || '';
      pd.organizerScore = self.game.OrganizerScore;
      pd.opponentScore = self.game.OpponentScore;
      pd.organizerTricks = self.game.OrganizerTricks;
      pd.opponentTricks = self.game.OpponentTricks;
      pd.dealer = self.game.Dealer;
      pd.turn = self.game.Turn;
      pd.cardFaceUp = self.game.CardFaceUp;
      pd.aco = self.game.ACO;
      pd.acp = self.game.ACP;
      pd.acl = self.game.ACL;
      pd.acr = self.game.ACR;
      pd.po = self.game.PO;
      pd.pp = self.game.PP;
      pd.pl = self.game.PL;
      pd.pr = self.game.PR;
      
      $.ajax({
        method: 'POST',
        url: 'api/logGameControllerState.php',
        data: pd,
        success: function (response) {
          try {
            var data = JSON.parse(response);
            if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            }
          } catch (error) {
            app.errorVM.add('Error ' + ': ' + error.message || error);
            console.log(error.stack);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
        }
      });
    };

    self.setTurnDealerSkipped = function(){
      $.ajax({
        method: 'POST',
        url: 'api/setTurnDealerSkipped.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            var data = JSON.parse(response);
            if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            }
          } catch (error) {
            app.errorVM.add('Error ' + ': ' + error.message || error);
            console.log(error.stack);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
        }
      });
    };
    
    self.gotoWaitForPlay = function(){
      self.clearAcknowledgments();
      self.setExecutionPoint('waitForPlay');
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
        // the dealer has been determined
        if (!self.game.DealID) {
          // the deal api has not completed.
          self.setExecutionPoint('dealOrWaitForCardFaceUp');
        }
        if (self.game.allCardsHaveBeenPlayed()) {
          if (self.position == 'O') {
            self.setExecutionPoint('scoreHand');
          } else {
            self.setExecutionPoint('waitForScore');
          }
        } else {
          if (self.game.OpponentTrump || self.game.OrganizerTrump) {
            // trump has been established.
            self.gotoWaitForPlay();
          } else {
            self.setExecutionPoint('waitForTrump');
          }
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
      
      if ((self.position == 'O') && (self.firstjackTimer === null)) {
        self.firstjackOp.fn = self.getNextStartCard;
        self.firstjackTimer = setInterval(self.firstjack, 100);
      }
      
      if ((self.position != 'O') && (self.firstjackTimer === null)) {
        self.firstjackOp.fn = self.getCurrentStartCard;
        self.firstjackTimer = setInterval(self.firstjack, 100);
      }
    };
    
    self.dealOrWaitForCardFaceUpFn = function(){
      if (self.position === self.game.Dealer) {
        self.deal();
      }
      self.setExecutionPoint('waitForCardFaceUp');
    }
    
    self.waitForAcknowledgmentsFn = function(){
      if (self.game.allPlayersHaveAcknowledged()) {
        // all the players have acknowledged seeing the first Jack.
        self.clearTable();
        if (self.position == 'O') {
          self.setDealPosition(self.firstDealer, 'isFirst');  // todo: rename it self.setFirstDealPosition() and remove the parameter.
        }
        self.setExecutionPoint('dealOrWaitForCardFaceUp');
      }
    };

    self.waitForCardFaceUpFn = function(){
      if (self.game.CardFaceUp.length > 1) {
        self.setExecutionPoint('waitForTrump');
      }
    };
    
    self.waitForTrumpFn = function(){
      // The heartbeat loop is running and will cause the page to be updated.
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
        if (self.game.CardFaceUp.length == 5 && self.game.CardFaceUp[4] == self.game.Dealer) {
          // The partner of the dealer took it alone.  The dealer is the skipped player.
          // Let the organizer set Turn.
          if (self.position == 'O') {
            self.setTurnDealerSkipped();
          }
          self.gotoWaitForPlay();
        } else {
          self.setExecutionPoint('waitForDiscard');
        }
      }
      if (trump) {
        self.gotoWaitForPlay();
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
      
      self.placeCardWithAcknowledge(self.game.PO, 'O');
      self.placeCardWithAcknowledge(self.game.PP, 'P');
      self.placeCardWithAcknowledge(self.game.PL, 'L');
      self.placeCardWithAcknowledge(self.game.PR, 'R');
      
      if (self.game.allCardsHaveBeenPlayed() || self.game.ScoringInProgress) {
        if (self.position == 'O') {
          self.delay = self.game.Speed > 0 ? 1 : 0;
          self.setExecutionPoint('scoreHand');
        } else {
          if (self.game.Speed == 0) {
            self.delay = app.clearTableDelay;
            self.setExecutionPoint('delayAsPlayer');
          } else {
            self.delay = 0;
            self.setExecutionPoint('clearTableAsPlayer');
          }
        }
      }
    };
    
    self.scoreHandFn = function(){
      var winner = self.playerInfoVM.getWinnerOfHand();
      var newScore = self.playerInfoVM.getNewScore(winner);
      self.logHand(newScore);
      self.updateScoreAfterHand(winner, newScore); // sets ScoringInProgress to '1'; self.game.allCardsHaveBeenPlayed() will now be false.
      self.setExecutionPoint('clearTableAsOrganizer');
    }
    
    self.delayAsPlayerFn = function() {
      self.delay = self.delay - 1 > 0 ? self.delay - 1 : 0;
      if (self.delay == 0) {
        self.setExecutionPoint('clearTableAsPlayer');
      }
    }
    
    self.clearTableAsPlayerFn = function(){
      self.clearTable();
      self.previousPO = ''; self.previousPP = ''; self.previousPL = ''; self.previousPR = '';

      if (self.game.ScoringInProgress) {
        self.acknowledge(self.position);
        self.setExecutionPoint('waitForScore');
      }
    };
    
    self.clearTableAsOrganizerFn = function(){
      self.previousPO = ''; self.previousPP = ''; self.previousPL = ''; self.previousPR = '';
      
      if (self.game.allPlayersHaveAcknowledged()) {
        self.delay = 0;
        self.clearTable();
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
            self.finishGame();
            self.setExecutionPoint('showGameOverDialog');
          } else {
            self.setExecutionPoint('dealOrWaitForCardFaceUp');
          }
        } else {
          self.gotoWaitForPlay();
        }
      }
    };
    
    self.waitForDiscardFn = function(){
      if (self.game.CardFaceUp.length > 3 && self.game.CardFaceUp[2] == 'S') {
          self.gotoWaitForPlay();
      }
    };
    
    self.showGameOverDialogFn = function(){
      self.finishGameDialogVM.update();
      self.finishGameModal.show();
      clearInterval(self.heartbeatTimer);
    };
    
    self.firstjack = function(){
      if (!self.firstjackOp.isStarted) {
        // console.log('starting firstjackOp ...');
        self.firstjackOp.start(Date.now() + app.times.firstJackTime);
        self.firstjackOp.fn();
      } else if (self.firstjackOp.succeeded === true && Date.now() > self.firstjackOp.expirationTime) {
        self.firstjackOp.isStarted = false;
      }
    };
    
    self.heartbeat = function(){
      if (!self.heartbeatOp.isStarted) {
        self.heartbeatOp.start(Date.now() + app.times.gameTime);
        self.getGame();
      } else if (self.heartbeatOp.succeeded === true && Date.now() > self.heartbeatOp.expirationTime) {
        self.heartbeatOp.isStarted = false;
      }
    };
    
    self.gameExecution = [
      { id: 'initialize', fn: self.initializeFn },
      { id: 'selectFirstJack', fn: self.selectFirstJackFn },
      { id: 'waitForAcknowledgments', fn: self.waitForAcknowledgmentsFn },
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
      { id: 'delayAsPlayer', fn: self.delayAsPlayerFn },
    ];
    
    self.initialize = function(){
      self.setExecutionPoint('initialize');
      self.heartbeatTimer = setInterval(self.heartbeat, 100);
    }
    
    self.initialize();
    
  }
  
  $(function (){
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
    ko.applyBindings(gc.finishGameDialogVM, $('#finishGameModal')[0]);
    app.errorVM = new errorViewModel();
    ko.applyBindings(app.errorVM, $('#ErrorInfo')[0]);
  });
  
</script>
