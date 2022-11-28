<?php 
  include_once('../../config/config.php'); 
?>

<script type="text/javascript">
  
  // The gameController is responsible for calling on viewmodels to update the score, and do animations on 
  // the play table.  The main interval timer is the getGameInterval which runs throughout the game.
  function gameController() {
    var self = this;
    
    self.game = new gameModel({});
    self.executionPoint = app.state.selectFirstJack;
    self.position = null;
    self.playerID = '<?php echo "{$_COOKIE[$cookieName]}"; ?>';
    self.nPlayerInfoVM = new playerInfoViewModel();
    self.ePlayerInfoVM = new playerInfoViewModel();
    self.wPlayerInfoVM = new playerInfoViewModel();
    self.playerInfoVM = new currentPlayerInfoViewModel();
    self.myScoreVM = new scoreViewModel();
    self.opponentScoreVM = new scoreViewModel();
    self.playVM = new playViewModel();
    self.getGameInterval = null;
    self.orgGetNextFInterval = null;
    self.playerGetCurrentFInterval = null;
    self.firstDealer = ' ';
    
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

    // todo: organize gameController. can it be modularized?
    self.setExecutionPoint = function(descr, id) {
      console.log('execution point: ' + descr);
      self.executionPoint = id;
    };
    
    self.updateScoresAndInfo = function() {
      self.nPlayerInfoVM.update(self.game);
      self.ePlayerInfoVM.update(self.game);
      self.wPlayerInfoVM.update(self.game);
      self.playerInfoVM.update(self.game);
      self.myScoreVM.update(self.game);
      self.opponentScoreVM.update(self.game);
      if (self.game.CardFaceUp.length == 2) {
        self.playVM.faceupCardURL(app.getCardURL(self.game.CardFaceUp.substr(0,2)));
        $('#cardFaceUp').show();
      } else if (self.game.CardFaceUp.length == 3) {
        self.playVM.faceupCardURL(app.getCardURL('cardback'));
        $('#cardFaceUp').show();
      } else if (self.game.CardFaceUp.length == 4) {
        $('#cardFaceUp').hide();
      } 
    };

    self.acknowledgeJack = function(position) {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.position = position;
      $.ajax({
        method: 'POST',
        url: 'api/acknowledgeJack.php',
        data: pd,
        success: function (response) {
          console.log('acknowledgeJack response', response);
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };

    self.getCurrentStartCard = function(){
      $.ajax({
        method: 'POST',
        url: 'api/getCurrentStartCard.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ID) {
              var i = app.positions.indexOf(self.position);
              self.placeFirstJack[i](data.ID, data.Position);
              if (data.ID[0] == 'J') {
                self.acknowledgeJack(self.position);
                self.setExecutionPoint('waitForAcknowledgements', app.state.waitForAcknowledgements);
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
      $.ajax({
        method: 'POST',
        url: 'api/getNextStartCard.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            var i = app.positions.indexOf(self.position);
            self.placeFirstJack[i](data.ID, data.Position);
            if (data.ID[0] == 'J') {
              self.firstDealer = data.Position;
              self.setExecutionPoint('waitForAcknowledgements', app.state.waitForAcknowledgements);
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

    // Turn and Dealer has been set in self.game.  self.position is the dealer. 
    // The deal api selects a random deal where `PurposeCode` = 'D' and DealID has not yet been used in this game.
    // It distributes cards to players in table `Play`.
    self.deal = function() {
      $.ajax({
        method: 'POST',
        url: 'api/deal.php',
        data: app.apiPostData,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) console.log(data.ErrorMsg);
            self.setExecutionPoint('waitForCardFaceUp', app.state.waitForCardFaceUp);
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
          self.setExecutionPoint('dealOrWaitForCardFaceUp', app.state.dealOrWaitForCardFaceUp);
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.getGameInterval);
        }
      });
    };

    self.placeFirstJack = [
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

    self.clearBoard = function(){
      self.playVM.nCardURL('');  self.playVM.eCardURL('');  self.playVM.sCardURL('');  self.playVM.wCardURL('');
    };
    
    self.getGame = function() {
      $.ajax({
        method: 'POST',
        url: 'api/getGame.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            self.updateScoresAndInfo();
            self.game = new gameModel(JSON.parse(response));
            self.gameExecution[self.executionPoint]();
          } catch (error) {
            console.log('Error ' + ': ' + error.message);
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

    self.initializeFn = function(){
      self.setThisPlayerPosition(self.game);
      self.nPlayerInfoVM.initialize('N', self.position, self.game);
      self.ePlayerInfoVM.initialize('E', self.position, self.game);
      self.wPlayerInfoVM.initialize('W', self.position, self.game);
      self.playerInfoVM.initialize('S', self.position, self.game);
      self.myScoreVM.initialize('M', self.position, self.game);
      self.opponentScoreVM.initialize('O', self.position, self.game);
      
      if (self.game.Dealer.length > 0) {
        self.setExecutionPoint('waitForMyTurn', app.state.waitForMyTurn);
      } else {
        self.setExecutionPoint('selectFirstJack', app.state.selectFirstJack);
      }
      
      // for debugging:
      // self.deal();
    };
    
    self.selectFirstJackFn = function(){
      if (!self.position)
        throw "self.position is null";
      
      if (self.game.Dealer)
        throw "selectFirstJackFn(): dealer is already set.";
      
      if ((self.position == 'O') && (self.orgGetNextFInterval === null)) {
        console.log('Starting the first Jack selection timer.  ');
        self.orgGetNextFInterval = setInterval(self.getNextStartCard, app.times.firstJackTime);
      }
      
      if ((self.position != 'O') && (self.playerGetCurrentFInterval === null)) {
        console.log('Starting the first Jack query timer.  ');
        self.playerGetCurrentFInterval = setInterval(self.getCurrentStartCard, app.times.firstJackTime);
      }
    };
    
    self.idleFn = function() {
    };
    
    self.dealOrWaitForCardFaceUpFn = function() {
      if (self.game.Dealer === self.position) {
        self.deal();
      } else {
        self.setExecutionPoint('waitForCardFaceUp', app.state.waitForCardFaceUp);
      }
    }
    
    self.waitForAcknowledgementsFn = function(){
      if (self.game.ACP == 'A' && self.game.ACR == 'A' && self.game.ACL == 'A') {
        // all the players have acknowledged seeing the first Jack.
        self.clearBoard();
        if (self.position == 'O') {
          self.setDealPosition(self.firstDealer, 'isFirst');
        } else {
          self.setExecutionPoint('dealOrWaitForCardFaceUp', app.state.dealOrWaitForCardFaceUp);
        }
      }
    };

    self.waitForCardFaceUpFn = function(){
      if (self.game.CardFaceUp[0] != ' ') {
        self.playVM.faceupCardURL(app.getCardURL(self.game.CardFaceUp.substr(0,2)));
        $('#cardFaceUp').show();
        // $('#cardFaceUp').toggleClass('hover'); // doesn't work on iPad.
        // if it's my turn call self.playerInfoVM.enableBid();
        // otherwise, wait for my turn.
        self.setExecutionPoint('waitForMyTurn', app.state.waitForMyTurn);
      }
    };
    
    self.waitForMyTurnFn = function() {
      // The getGameInterval timer is running and will update the page.
      // The currentPlayerInfoViewModel.update() method is called on 
      // every heartbeat.  That VM needs to be smart enough to work
      // efficiently based on the game state determined by several
      // fields in gameModel.
    }
    
    // this has to be parallel to const state {}.  so confusing.
    self.gameExecution = [
      self.initializeFn,       // 0
      self.selectFirstJackFn,  // 1
      self.waitForAcknowledgementsFn, // 2
      self.dealFn,             // 3
      self.chooseTrumpFn,      // 4
      self.idleFn,             // 5
      self.dealOrWaitForCardFaceUpFn, // 6
      self.waitForCardFaceUpFn, // 7
      self.waitForMyTurnFn // 8
    ];

    self.initialize = function() {
      // todo: before doing any of these things, make sure it is not already done. 
      // where is game state saved?  [It's a complicated set of conditions.]
      // 1) decide who the dealer is.
      // 2) the organizer can call api/getNextStartCard().  Everyone else can call api/getCurrentStartCard().
      // 3) everyone needs to see who got the first jack. Everyone needs to acknowledge that before the dealer is announced.
      //    api/acknowledgeFirstJack()
      self.setExecutionPoint('initialize', app.state.initialize);
      self.getGameInterval = setInterval(self.getGame, app.times.gameTime);
    }
    
    self.initialize();

  }
  
  $(function () {
    var gc = new gameController();
    ko.applyBindings(gc.nPlayerInfoVM, $('#NorthInfo')[0]);
    ko.applyBindings(gc.ePlayerInfoVM, $('#EastInfo')[0]);
    ko.applyBindings(gc.wPlayerInfoVM, $('#WestInfo')[0]);
    ko.applyBindings(gc.myScoreVM, $('#MyScore')[0]);
    ko.applyBindings(gc.opponentScoreVM, $('#OpponentScore')[0]);
    ko.applyBindings(gc.playVM, $('#PlayTable')[0]);
    ko.applyBindings(gc.playerInfoVM, $('#SouthInfo')[0]);
  });
  
</script>
