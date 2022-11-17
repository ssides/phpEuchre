<?php 
  include_once('../../config/config.php'); 
?>

<script type="text/javascript">

// todo: put these in their own object.
  const times = {
    firstJackTime: 600,
    gameTime: 1000
  };
  
  const positions = 'OPLR';
  const appURL = '<?php echo $appUrl; ?>';


  function gameModel(data) {
    this.Organizer = data.Organizer || '';
    this.Partner = data.Partner || '';
    this.Left = data.Left || '';
    this.Right = data.Right || '';
    this.GameStartDate = data.GameStartDate || '';
    this.Dealer = data.Dealer || '';
    this.Turn = data.Turn || '';
    this.CardFaceUp = data.CardFaceUp || '    ';
    this.OrganizerTrump = data.OrganizerTrump || '';
    this.OrganizerTricks = data.OrganizerTricks || '';
    this.OrganizerScore = data.OrganizerScore || '';
    this.OpponentTrump = data.OpponentTrump || '';
    this.OpponentTricks = data.OpponentTricks || '';
    this.OpponentScore = data.OpponentScore || '';
    this.AJP = data.AJP || '';
    this.AJR = data.AJR || '';
    this.AJL = data.AJL || '';
    this.OThumbnailURL = data.OThumbnailURL || '';
    this.OName = data.OName || '';
    this.PThumbnailURL = data.PThumbnailURL || '';
    this.PName = data.PName || '';
    this.LThumbnailURL = data.LThumbnailURL || '';
    this.LName = data.LName || '';
    this.RThumbnailURL = data.RThumbnailURL || '';
    this.RName = data.RName || '';
  }
  
  function playerInfoViewModel() {
    var self = this;

    self.ix = 0;
    self.myPosition = ' ';
    self.thumbnailURL = ko.observable('');
    self.name = ko.observable('');
    self.dealer = ko.observable(' ');
    self.infoStatus = ko.observable('infoBorder');
    self.aSetURL = [
      function(){},
      function(gameData){
        self.thumbnailURL(gameData.OThumbnailURL);
        self.name(gameData.OName);
      },
      function(gameData){
        self.thumbnailURL(gameData.PThumbnailURL);
        self.name(gameData.PName);
      },
      function(gameData){
        self.thumbnailURL(gameData.LThumbnailURL);
        self.name(gameData.LName);
      },
      function(gameData){
        self.thumbnailURL(gameData.RThumbnailURL);
        self.name(gameData.RName);
      }
    ];
    self.setURL = function(gameData) { 
      self.aSetURL[self.ix](gameData) 
    };
    self.update = function(gameData) { 
      if (self.myPosition == gameData.Dealer) {
        self.dealer('D');
      } else {
        self.dealer(' ');
      }
      if (self.myPosition == gameData.Turn) {
        self.infoStatus('infoTurnBorder');
      } else {
        self.infoStatus('infoBorder');
      }
    };
    
    self.initialize = function(iam, selfPosition, gameData){
      if ((iam == 'N' && selfPosition == 'P')
          || (iam == 'S' && selfPosition == 'O')
          || (iam == 'E' && selfPosition == 'L')
          || (iam == 'W' && selfPosition == 'R')) {
        self.myPosition = 'O';
        self.ix = 1; // use Organizer data
      } else if ((iam == 'N' && selfPosition == 'O')
          || (iam == 'S' && selfPosition == 'P')
          || (iam == 'E' && selfPosition == 'R')
          || (iam == 'W' && selfPosition == 'L')) {
        self.myPosition = 'P';
        self.ix = 2; // use Partner data
      } else if ((iam == 'N' && selfPosition == 'R')
          || (iam == 'S' && selfPosition == 'L')
          || (iam == 'E' && selfPosition == 'P')
          || (iam == 'W' && selfPosition == 'O')) {
        self.myPosition = 'L';
        self.ix = 3; // use Left data
      } else if ((iam == 'N' && selfPosition == 'L')
          || (iam == 'S' && selfPosition == 'R')
          || (iam == 'E' && selfPosition == 'O')
          || (iam == 'W' && selfPosition == 'P')) {
        self.myPosition = 'R';
        self.ix = 4; // use Right data
      }
      
      self.setURL(gameData);
    };
  }
  
  function scoreViewModel() {
    var self = this;
    
    self.ix = 0;
    self.label = ko.observable('');
    self.score = ko.observable(0);
    self.trumpURL = ko.observable('');
    self.tricks = ko.observable('');
    
    self.getTricks = function(t){
      var tricks = '';
      for(let i = 0; i < t; i++) {
        tricks += '&#149;&nbsp;';
      }
      return tricks;
    };
    self.aUpdate = [
      function(){ },
      function(gameData){
        if (gameData.OrganizerTrump) {
          self.trumpURL(appURL + 'content/images/cards/' + gameData.OrganizerTrump[0] + '.png');
        } else {
          self.trumpURL('');
        }
        if (gameData.OrganizerTricks) {
          self.tricks(self.getTricks(gameData.OrganizerTricks));
        } else {
          self.tricks('&nbsp;');
        }
        self.score(gameData.OrganizerScore);
      },
      function(gameData){
        if (gameData.OpponentTrump) {
          self.trumpURL(appURL + 'content/images/cards/' + gameData.OpponentTrump[0] + '.png');
        } else {
          self.trumpURL('');
        }
        if (gameData.OpponentTricks) {
          self.tricks(self.getTricks(gameData.OpponentTricks));
        } else {
          self.tricks('&nbsp;');
        }
        self.score(gameData.OpponentScore);
      },
    ];
    self.update = function(gameData){
      self.aUpdate[self.ix](gameData);
    };
    
    self.initialize = function(iam, selfPosition, gameData){
      if (iam == 'M' && (selfPosition == 'O' || selfPosition == 'P')) {
        self.label('Us');
        self.ix = 1;  // use Organizer data
      } else if (iam == 'M' && (selfPosition == 'L' || selfPosition == 'R')) {
        self.label('Us');
        self.ix = 2;  // use Opponent data
      } else if (iam == 'O' && (selfPosition == 'O' || selfPosition == 'P')) {
        self.label('Them');
        self.ix = 2;  // use Opponent data
      } else if (iam == 'O' && (selfPosition == 'L' || selfPosition == 'R')) {
        self.label('Them');
        self.ix = 1;  // use Organizer data
      }
    };
  }
  
  function playViewModel() {
    var self = this;
    
    self.nCardURL = ko.observable('');
    self.eCardURL = ko.observable('');
    self.sCardURL = ko.observable('');
    self.wCardURL = ko.observable('');
    self.faceupCardURL = ko.observable('');
    
  }
  
  const state = {
    initialize: 0,
    selectFirstJack: 1,
    waitForAcknowledgements: 2,
    deal: 3,
    chooseTrump: 4,
    play: 5,
    getMyCards: 6,
    idle: 7,
    dealOrWaitForCardFaceUp: 8,
    getMyCards: 9,
    waitForCardFaceUp: 10
  };

  function gameController() {
    var self = this;
    
    self.game = new gameModel({});
    self.executionPoint = state.selectFirstJack;
    self.position = null;
    self.playerID = '<?php echo "{$_COOKIE[$cookieName]}"; ?>';
    self.postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'"
                      .",gameID:'{$_SESSION['gameID']}'"   ?>  };
    self.nPlayerInfoVM = new playerInfoViewModel();
    self.sPlayerInfoVM = new playerInfoViewModel();
    self.ePlayerInfoVM = new playerInfoViewModel();
    self.wPlayerInfoVM = new playerInfoViewModel();
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
    
    self.updateScores = function() {
      self.nPlayerInfoVM.update(self.game);
      self.sPlayerInfoVM.update(self.game);
      self.ePlayerInfoVM.update(self.game);
      self.wPlayerInfoVM.update(self.game);
      self.myScoreVM.update(self.game);
      self.opponentScoreVM.update(self.game);
    };
    
    self.getCardURL = function(cardID){
      return appURL + 'content/images/cards/' + cardID + '.png';
    };

    self.acknowledgeJack = function(position) {
      var pd = {};
      Object.assign(pd, self.postData);
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
        data: self.postData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ID) {
              var i = positions.indexOf(self.position);
              self.placeFirstJack[i](data.ID, data.Position);
              if (data.ID[0] == 'J') {
                self.acknowledgeJack(self.position);
                self.setExecutionPoint('waitForAcknowledgements', state.waitForAcknowledgements);
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
        data: self.postData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            var i = positions.indexOf(self.position);
            self.placeFirstJack[i](data.ID, data.Position);
            if (data.ID[0] == 'J') {
              self.firstDealer = data.Position;
              self.setExecutionPoint('waitForAcknowledgements', state.waitForAcknowledgements);
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
      console.log('deal');
      $.ajax({
        method: 'POST',
        url: 'api/deal.php',
        data: self.postData,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) console.log(data.ErrorMsg);
            self.setExecutionPoint('waitForCardFaceUp', state.waitForCardFaceUp);
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
      Object.assign(pd, self.postData);
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
          self.setExecutionPoint('dealOrWaitForCardFaceUp', state.dealOrWaitForCardFaceUp);
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
          self.playVM.sCardURL(self.getCardURL(cardID));
        else if (atPosition == 'P') 
          self.playVM.nCardURL(self.getCardURL(cardID));
        else if (atPosition == 'L') 
          self.playVM.wCardURL(self.getCardURL(cardID));
        else
          self.playVM.eCardURL(self.getCardURL(cardID));
      },
      function(cardID, atPosition) { 
        if (atPosition == 'O') 
          self.playVM.nCardURL(self.getCardURL(cardID));
        else if (atPosition == 'P') 
          self.playVM.sCardURL(self.getCardURL(cardID));
        else if (atPosition == 'L') 
          self.playVM.eCardURL(self.getCardURL(cardID));
        else
          self.playVM.wCardURL(self.getCardURL(cardID));
      },
      function(cardID, atPosition) { 
        if (atPosition == 'O') 
          self.playVM.eCardURL(self.getCardURL(cardID));
        else if (atPosition == 'P') 
          self.playVM.wCardURL(self.getCardURL(cardID));
        else if (atPosition == 'L') 
          self.playVM.sCardURL(self.getCardURL(cardID));
        else
          self.playVM.nCardURL(self.getCardURL(cardID));
      },
      function(cardID, atPosition) { 
        if (atPosition == 'O') 
          self.playVM.wCardURL(self.getCardURL(cardID));
        else if (atPosition == 'P') 
          self.playVM.eCardURL(self.getCardURL(cardID));
        else if (atPosition == 'L') 
          self.playVM.nCardURL(self.getCardURL(cardID));
        else
          self.playVM.sCardURL(self.getCardURL(cardID));
      }
    ];

    self.clearBoard = function(){
      self.playVM.nCardURL('');  self.playVM.eCardURL('');  self.playVM.sCardURL('');  self.playVM.wCardURL('');
    };
    
    self.getGame = function() {
      $.ajax({
        method: 'POST',
        url: 'api/getGame.php',
        data: self.postData,
        success: function (response) {
          try {
            self.game = new gameModel(JSON.parse(response));
            self.updateScores();
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
      self.sPlayerInfoVM.initialize('S', self.position, self.game);
      self.ePlayerInfoVM.initialize('E', self.position, self.game);
      self.wPlayerInfoVM.initialize('W', self.position, self.game);
      self.myScoreVM.initialize('M', self.position, self.game);
      self.opponentScoreVM.initialize('O', self.position, self.game);
      
      // todo: figure out the game state, and figure out where to go from here.
      self.setExecutionPoint('selectFirstJack', state.selectFirstJack);
    };
    
    self.selectFirstJackFn = function(){
      if (!self.position)
        throw "self.position is null";
      
      if (self.game.Dealer != 'N')
        throw "selectFirstJackFn(): dealer is already set.";
      
      if ((self.position == 'O') && (self.orgGetNextFInterval === null)) {
        console.log('Starting the first Jack selection timer.  ');
        self.orgGetNextFInterval = setInterval(self.getNextStartCard, times.firstJackTime);
      }
      
      if ((self.position != 'O') && (self.playerGetCurrentFInterval === null)) {
        console.log('Starting the first Jack query timer.  ');
        self.playerGetCurrentFInterval = setInterval(self.getCurrentStartCard, times.firstJackTime);
      }
    };

    self.playFn = function() {
      console.log('play');
    };
    
    self.idleFn = function() {
      console.log('idle');
    };
    
    self.dealOrWaitForCardFaceUpFn = function() {
      if (self.game.Dealer === self.position) {
        self.deal();
      } else {
        self.setExecutionPoint('waitForCardFaceUp', state.waitForCardFaceUp);
      }
    }
    
    self.waitForAcknowledgementsFn = function(){
      if (self.game.AJP == 'A' && self.game.AJR == 'A' && self.game.AJL == 'A') {
        // all the players have acknowledged seeing the first Jack.
        self.clearBoard();
        if (self.position == 'O') {
          self.setDealPosition(self.firstDealer, 'isFirst');
        } else {
          self.setExecutionPoint('dealOrWaitForCardFaceUp', state.dealOrWaitForCardFaceUp);
        }
      }
    };

    self.getMyCardsFn = function(){
      
    };

    self.waitForCardFaceUpFn = function(){
      if (self.game.CardFaceUp[0] != ' ') {
        self.playVM.faceupCardURL(self.getCardURL(self.game.CardFaceUp.substr(0,2)));
        $('#cardFaceUp').show();
        $('#cardFaceUp').toggleClass('hover');
        self.setExecutionPoint('idle', state.idle);
      }
    };
    
    // this has to be parallel to const state {}.  so confusing.
    self.gameExecution = [
      self.initializeFn,       // 0
      self.selectFirstJackFn,  // 1
      self.waitForAcknowledgementsFn, // 2
      self.dealFn,             // 3
      self.chooseTrumpFn,      // 4
      self.playFn,             // 5
      self.getMyCardsFn,       // 6
      self.idleFn,             // 7
      self.dealOrWaitForCardFaceUpFn, // 8
      self.getMyCardsFn,       // 9
      self.waitForCardFaceUpFn // 10
    ];

    self.initialize = function() {
      // todo: before doing any of these things, make sure it is not already done. where is game state saved? 
      // 1) decide who the dealer is.
      // 2) the organizer can call api/getNextStartCard().  Everyone else can call api/getCurrentStartCard().
      // 3) everyone needs to see who got the first jack. Everyone needs to acknowledge that before the dealer is announced.
      //    api/acknowledgeFirstJack()
      self.setExecutionPoint('initialize', state.initialize);
      self.getGameInterval = setInterval(self.getGame, times.gameTime);
    }
    
    self.initialize();

  }
  
  $(function () {
    var gc = new gameController();
    ko.applyBindings(gc.nPlayerInfoVM, $('#NorthInfo')[0]);
    ko.applyBindings(gc.sPlayerInfoVM, $('#SouthInfo')[0]);
    ko.applyBindings(gc.ePlayerInfoVM, $('#EastInfo')[0]);
    ko.applyBindings(gc.wPlayerInfoVM, $('#WestInfo')[0]);
    ko.applyBindings(gc.myScoreVM, $('#MyScore')[0]);
    ko.applyBindings(gc.opponentScoreVM, $('#OpponentScore')[0]);
    ko.applyBindings(gc.playVM, $('#PlayTable')[0]);
  });
  
</script>
