<?php 
  include_once('../../config/config.php'); 
?>

<script type="text/javascript">
  
  function playViewModel() {
    var self = this;
    
    self.appURL = '<?php echo $appUrl; ?>';
    self.playerID = '<?php echo "{$_COOKIE[$cookieName]}"; ?>';
    self.postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'"
                      .",gameID:'{$_SESSION['gameID']}'"   ?>  };
    self.position = null;
    self.game = {};
    self.orgGetNextFTimer = null;
    self.getGameTimer = null;
    self.playerGetCurrentFTimer = null;
    self.nCardURL = ko.observable('');
    self.eCardURL = ko.observable('');
    self.sCardURL = ko.observable('');
    self.wCardURL = ko.observable('');
    
    self.getFirstJackURL = function(cardID){
      return self.appURL + 'content/images/cards/' + cardID + '.jpg';
    };
    
    self.positions = 'OPLR';
    self.placeFirstJack = [
      function(cardID, atPosition) { 
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
              clearInterval(self.orgGetNextFTimer);
              self.nCardURL('');
              self.eCardURL('');
              self.sCardURL('');
              self.wCardURL('');
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
    
    self.getGame = function() {
      $.ajax({
        method: 'POST',
        url: 'api/getGame.php',
        data: self.postData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if ((self.playerID === data.Organizer)) {
              self.position = 'O';
            } else if (self.playerID === data.Left) {
              self.position = 'L';
            } else if (self.playerID === data.Right) {
              self.position = 'R';
            } else if (self.playerID === data.Partner) {
              self.position = 'P';
            }
            if ((self.position == 'O') && (self.orgGetNextFTimer === null)) {
              console.log('Starting the first Jack selection timer.  ');
              self.orgGetNextFTimer = setInterval(self.getNextStartCard, 750);
            }
            if ((self.position != 'O') && (self.playerGetCurrentFTimer === null)) {
              console.log('Starting the first Jack query timer.  ');
              self.playerGetCurrentFTimer = setInterval(self.getCurrentStartCard, 750);
            }
            Object.assign(self.game, data);
          } catch (error) {
            console.log('Could not parse response from getGame. ' + error + ': ' + response);
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
      self.getGameTimer = setInterval(self.getGame, 1000);
      // there needs to be a timer for non-organizer players called getCurrentStartCard. and acknowledgeJack(self.position);
    }
    
    self.initialize();
  }    
  
  $(function () {
    ko.applyBindings(new playViewModel());
  });
  
</script>
