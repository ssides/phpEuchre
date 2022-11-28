<script type="text/javascript">

  // Most of the Euchre rules are coded here. The update() method is called on every 
  // heartbeat, and determines the game state using several fields in the gameModel.
  // This viewmodel controls what happens inside the SouthInfo div block in play.php.
  function currentPlayerInfoViewModel() {
    var self = this;

    self.myPosition = ' ';
    self.gameData = new gameModel({});
    self.dealer = ko.observable(' ');
    self.bidDialog = false;
    self.infoStatus = ko.observable('infoBorder');
    self.dbgCard1 = ko.observable('');
    self.dbgCardURL = ko.observable('');
    self.alone = ko.observable('');
    self.cards = ko.observableArray();
    self.sortedCards = ko.pureComputed(function(){
      return self.cards.slice().sort(function(a,b){
        return a.suit === b.suit ? (a.rank == b.rank ? 0 : a.rank < b.rank ? -1 : 1) : a.suit < b.suit ? -1 : 1;
      });
    });
    self.isMyTurn = false;
    self.iamDealer = false;
    
    self.selectCard = function(card){
      // if it's my turn and if trump has been selected.
      if (self.isMyTurn && (self.gameData.OrganizerTrump.length > 0 || self.gameData.OpponentTrump.length > 0)) {
        self.cards().forEach(function(c){ 
          c.isSelected(c.id == card.id ? true : false);
        });
      }
    };
    
    self.getCardObject = function(c) {
      var o = {
        id: c, 
        url: app.getCardURL(c),
        suit: self.getSuitOrder(c[1]),
        rank: self.getNonTrumpRank(c[0]),
        isPlayable: ko.observable(true),
        isSelected: ko.observable(false)
      };
      return o;
    };
    
    self.getSuitOrder = function(s) {
      return s == 'D' ? 1 : s == 'C' ? 2 : s == 'H' ? 3 : 4;
    };
    
    self.getNonTrumpRank = function(r) {
      return r == '9' ? 9 : r == '1' ? 10 : r == 'J' ? 11 : r == 'Q' ? 12 :r == 'K' ? 13 : 14;
    }

    self.getMyCards = function() {
      console.log('getMyCards(); position:',self.myPosition);
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.myPosition;
      $.ajax({
        method: 'POST',
        url: 'api/getMyCards.php',
        data: pd,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            } else {
              if (data.CardID1) {
                var c = [];
                if (data.CardID1.length == 2)
                  c.push(self.getCardObject(data.CardID1.substr(0,2)));
                if (data.CardID2.length == 2)
                  c.push(self.getCardObject(data.CardID2.substr(0,2)));
                if (data.CardID3.length == 2)
                  c.push(self.getCardObject(data.CardID3.substr(0,2)));
                if (data.CardID4.length == 2)
                  c.push(self.getCardObject(data.CardID4.substr(0,2)));
                if (data.CardID5.length == 2)
                  c.push(self.getCardObject(data.CardID5.substr(0,2)));
                
                // if it's the dealer and if someone ordered it up:
                // c.push(self.getCardObject(self.gameData.CardFaceUp.substr(0,2)));
                
                self.cards(c);
              }
            }
          } catch (error) {
            console.log('Could not parse response from getMyCards. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };
    
    self.setNextTurn = function() {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.myPosition;
      $.ajax({
        method: 'POST',
        url: 'api/setNextTurn.php',
        data: pd,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            }
          } catch (error) {
            console.log('Could not parse response from getMyCards. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };

    self.declineCard = function() {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.myPosition;
      $.ajax({
        method: 'POST',
        url: 'api/declineCard.php',
        data: pd,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            }
          } catch (error) {
            console.log('Could not parse response from declineCard. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });

    };
    
    self.pass = function() {
      $('#pass').prop('disabled', true);
      if (self.iamDealer && self.gameData.CardFaceUp.length == 2) {
        self.declineCard();
      } else {
        self.setNextTurn();
      }
    };

    self.play = function() {
      
    }
    
    self.discard = function() {
      $('#discard').prop('disabled', true);

    }
    
    self.pickItUp = function() {
      $('pickitup').prop('disabled', true);
    }
    
    self.actAccordingToRules = function() {
      if (self.gameData.CardFaceUp.length >= 2 && self.cards().length == 0) {
        self.getMyCards();
      }
      
      if (self.isMyTurn) {
        if (self.gameData.CardFaceUp.length == 2) {
          self.enableBid();
        }
        if (self.gameData.CardFaceUp.length == 3 && !self.bidDialog) {
          self.bidDialog = true;
          // pop up the bid dialog.
        }
      } else {
        self.hideButtons();
      }
    };
    
    // This will be called on every heartbeat in most states.
    self.update = function(gameData) { 
      self.isMyTurn = self.myPosition == gameData.Turn;
      self.iamDealer = self.myPosition == gameData.Dealer;
      self.gameData = new gameModel(gameData);
      
      if (self.myPosition == gameData.Dealer) {
        self.dealer('D');
      } else {
        self.dealer(' ');
      }
      if (self.isMyTurn) {
        self.infoStatus('infoTurnBorder');
      } else {
        self.infoStatus('infoBorder');
      }
      
      self.actAccordingToRules(gameData);
      
    };
    
    self.enableBid = function() {
      $('#pass').show();
      $('#pickitup').show();
      $('#alone').show();
      $('#lblAlone').show();
    };
    
    self.hideButtons = function() {
      $('#pass').hide();
      $('#pickitup').hide();
      $('#alone').hide();
      $('#lblAlone').hide();
    };
    
    self.initialize = function(iam, selfPosition, gameData){
      self.myPosition = selfPosition;
    };
  }
  
</script>