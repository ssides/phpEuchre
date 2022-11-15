<?php 
  include_once('../../config/config.php'); 
?>

<script type="text/javascript">

  const state = {
    selectFirstJack: 0,
    waitForAcknowledgements: 1,
    deal: 2,
    chooseTrump: 3
  };
  
  const times = {
    firstJackTime: 750,
    gameTime: 1000
  };
  
  function gameModel(data) {
    this.Organizer = data.Organizer || '';
    this.Partner = data.Partner || '';
    this.Left = data.Left || '';
    this.Right = data.Right || '';
    this.OrganizerScore = data.OrganizerScore || '';
    this.OpponentScore = data.OpponentScore || '';
    this.GameStartDate = data.GameStartDate || '';
    this.Dealer = data.Dealer || '';
    this.Trump = data.Trump || '';
    this.OrganizerTricks = data.OrganizerTricks || '';
    this.OpponentTricks = data.OpponentTricks || '';
    this.AJP = data.AJP || '';
    this.AJR = data.AJR || '';
    this.AJL = data.AJL || '';
    this.OThumbnailPath = data.OThumbnailPath || '';
    this.OName = data.OName || '';
    this.PThumbnailPath = data.PThumbnailPath || '';
    this.PName = data.PName || '';
    this.LThumbnailPath = data.LThumbnailPath || '';
    this.LName = data.LName || '';
    this.RThumbnailPath = data.RThumbnailPath || '';
    this.RName = data.RName || '';
  }
  
  function playViewModel() {
    var self = this;
    
    self.executionPoint = state.selectFirstJack;
    self.appURL = '<?php echo $appUrl; ?>';
    self.playerID = '<?php echo "{$_COOKIE[$cookieName]}"; ?>';
    self.postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'"
                      .",gameID:'{$_SESSION['gameID']}'"   ?>  };
    self.position = null;
    self.message = ko.observable('Selecting the dealer ...');
    self.game = new gameModel({});
    self.orgGetNextFTimer = null;
    self.playerGetCurrentFTimer = null;
    self.getGameTimer = null;
    self.nCardURL = ko.observable('');
    self.eCardURL = ko.observable('');
    self.sCardURL = ko.observable('');
    self.wCardURL = ko.observable('');
    
    // service functions
    self.getFirstJackURL = function(cardID){
      return self.appURL + 'content/images/cards/' + cardID + '.png';
    };
    
    self.clearBoard = function(){
      self.nCardURL('');  self.eCardURL('');  self.sCardURL('');  self.wCardURL('');
    };
    
    self.positions = 'OPLR';
    self.placeFirstJack = [
      function(cardID, atPosition) { 
        // todo: use a switch statement here, and in the rest of these functions.
        if (atPosition == 'O') 
          self.sCardURL(self.getFirstJackURL(cardID));
        else if (atPosition == 'P') 
          self.nCardURL(self.getFirstJackURL(cardID));
        else if (atPosition == 'L') 
          self.wCardURL(self.getFirstJackURL(cardID));
        else
          self.eCardURL(self.getFirstJackURL(cardID));
      },
      function(cardID, atPosition) { 
        if (atPosition == 'O') 
          self.nCardURL(self.getFirstJackURL(cardID));
        else if (atPosition == 'P') 
          self.sCardURL(self.getFirstJackURL(cardID));
        else if (atPosition == 'L') 
          self.eCardURL(self.getFirstJackURL(cardID));
        else
          self.wCardURL(self.getFirstJackURL(cardID));
      },
      function(cardID, atPosition) { 
        if (atPosition == 'O') 
          self.eCardURL(self.getFirstJackURL(cardID));
        else if (atPosition == 'P') 
          self.wCardURL(self.getFirstJackURL(cardID));
        else if (atPosition == 'L') 
          self.sCardURL(self.getFirstJackURL(cardID));
        else
          self.nCardURL(self.getFirstJackURL(cardID));
      },
      function(cardID, atPosition) { 
        if (atPosition == 'O') 
          self.wCardURL(self.getFirstJackURL(cardID));
        else if (atPosition == 'P') 
          self.eCardURL(self.getFirstJackURL(cardID));
        else if (atPosition == 'L') 
          self.nCardURL(self.getFirstJackURL(cardID));
        else
          self.sCardURL(self.getFirstJackURL(cardID));
      },
    ];
    
    self.setThisPlayerPosition = function(){
      if ((self.playerID === self.game.Organizer)) {
        self.position = 'O';
      } else if (self.playerID === self.game.Left) {
        self.position = 'L';
      } else if (self.playerID === self.game.Right) {
        self.position = 'R';
      } else if (self.playerID === self.game.Partner) {
        self.position = 'P';
      }
    };
    
    // execution point functions
    self.setExecutionPoint = function(descr, id) {
      console.log('execution point: ' + descr);
      self.executionPoint = id;
    };
    
    self.selectFirstJackFn = function(){
      if (self.position == null)
        self.setThisPlayerPosition();
      
      // `Dealer` != 'N' is better than these three checks. startGame in dashboard controller sets `Dealer` to 'N'
      if (self.game.AJP == 'A' && self.game.AJR == 'A' && self.game.AJL == 'A') {
        // do this if I'm the dealer. Otherwise go to getMyCards.
        // return to game is going to be tricky.
        self.setExecutionPoint('deal', state.deal);
      } else {
        if ((self.position == 'O') && (self.orgGetNextFTimer === null)) {
          console.log('Starting the first Jack selection timer.  ');
          self.orgGetNextFTimer = setInterval(self.getNextStartCard, times.firstJackTime);
        }
        
        if ((self.position != 'O') && (self.playerGetCurrentFTimer === null)) {
          console.log('Starting the first Jack query timer.  ');
          self.playerGetCurrentFTimer = setInterval(self.getCurrentStartCard, times.firstJackTime);
        }
      }
    };
    
    self.waitForAcknowledgementsFn = function(){
      if (self.game.AJP == 'A' && self.game.AJR == 'A' && self.game.AJL == 'A') {
        self.clearBoard();
        self.message('');
        self.setExecutionPoint('deal', state.deal);
      }
    };
    
    self.dealFn = function(){
      if (self.position == self.game.Dealer) {
        self.deal();
        self.setExecutionPoint('chooseTrump', state.chooseTrump);
      } else {
        self.getMyCards();
      }
    };
    
    self.chooseTrumpFn = function(){};
    
    self.gameExecution = [
      self.selectFirstJackFn,
      self.waitForAcknowledgementsFn,
      self.dealFn,
      self.chooseTrumpFn
    ];
    
    // api call functions
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
              // if data.ID[0] == 'J', acknowledge the jack, for self.position.
              console.log(data.ID, data.Position);
              var i = self.positions.indexOf(self.position);
              self.placeFirstJack[i](data.ID, data.Position);
              if (data.ID[0] == 'J') {
                self.acknowledgeJack(self.position);
                self.setExecutionPoint('waitForAcknowledgements', state.waitForAcknowledgements);
                clearInterval(self.playerGetCurrentFTimer);
              }
            } else {
              console.log(data.ErrorMsg);
            }
          } catch (error) {
            console.log('Could not parse response from getCurrentStartCard. ' + error + ': ' + response);
            clearInterval(self.playerGetCurrentFTimer);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.playerGetCurrentFTimer);
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
            console.log(data.ID, data.Position);
            var i = self.positions.indexOf(self.position);
            self.placeFirstJack[i](data.ID, data.Position);
            if (self.game.AJP == 'A' && self.game.AJR == 'A' && self.game.AJL == 'A') {
              self.setFirstDealPosition(data.Position);
              self.setExecutionPoint('waitForAcknowledgements', state.waitForAcknowledgements);
              clearInterval(self.orgGetNextFTimer);
              // the dealer is given in data.Position;
              // call api setDealer
            }
          } catch (error) {
            console.log('Could not parse response from getNextStartCard. ' + error + ': ' + response);
            clearInterval(self.orgGetNextFTimer);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.orgGetNextFTimer);
        }
      });
    };
    
    self.setFirstDealPosition = function(position){
      var pd = {};
      Object.assign(pd, self.postData);
      pd.position = position;
      $.ajax({
        method: 'POST',
        url: 'api/setFirstDealPosition.php',
        data: pd,
        success: function(response){
          console.log('setFirstDealPosition: ',response);
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.getGameTimer);
        }
      });
    };
    
    // The deal api selects a random deal where `PurposeCode` = 'D' and DealID has not yet been used in this game.
    // `Game`.`Dealer` was set by setFirstDealPosition.
    // For each player the deal api inserts into `Play`
    // In `Game` it updates `CardFaceUp` and `Turn`
    // While the dealer is dealing, the other players are calling the getMyCards api. which also returns `CardFaceUp` and `Turn`
    self.deal = function() {
      $.ajax({
        method: 'POST',
        url: 'api/deal.php',
        data: pd,
        success: function(response){
          
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.getGameTimer);
        }
      });
    };
    
    self.getMyCards = function(){
      console.log('get my cards');
    };
    
    // getGame event
    self.getGame = function() {
      $.ajax({
        method: 'POST',
        url: 'api/getGame.php',
        data: self.postData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            self.game = new gameModel(data);
            self.gameExecution[self.executionPoint]();
          } catch (error) {
            console.log('Error ' + ': ' + error.message);
            console.log(error.stack);
            clearInterval(self.getGameTimer);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          clearInterval(self.getGameTimer);
        }
      });
    };

    self.initialize = function() {
      // before doing any of these things, make sure it is not already done.
      // 1) decide who the dealer is.
      // 2) the organizer can call api/getNextStartCard().  Everyone else can call api/getCurrentStartCard().
      // 3) everyone needs to see who got the first jack. Everyone needs to acknowledge that before the dealer is announced.
      //    api/acknowledgeFirstJack()
      self.setExecutionPoint('selectFirstJack', state.selectFirstJack);
      self.getGameTimer = setInterval(self.getGame, times.gameTime);
    }
    
    self.initialize();
  }    
  
  $(function () {
    ko.applyBindings(new playViewModel());
  });
  
</script>
