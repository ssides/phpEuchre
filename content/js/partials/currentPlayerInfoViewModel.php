<script type="text/javascript">

  // Most of the Euchre rules are coded here. The update() method is called on every 
  // heartbeat, and determines the game state using several fields in the gameModel.
  // This viewmodel controls what happens inside the SouthInfo div block in play.php.
  // Popping up the dialog that lets the user select trump, whenever that is necessary
  // is handled by gameController, not currentPlayerInfoViewModel.
  function currentPlayerInfoViewModel() {
    var self = this;

    self.myPosition = '';
    self.trump = '';
    self.previousTrump = '';
    self.dealID = '';
    self.previousDealID = '';
    self.previousTurn = '';
    self.previousCardFaceUp = '';
    self.pickingItUp = false;
    self.discarded = false;
    self.gameData = new gameModel({});
    self.showPassBtn = ko.observable(false);
    self.enablePassBtn = ko.observable(true);
    self.showPlayBtn = ko.observable(false);
    self.enablePlayBtn = ko.observable(true);
    self.showPickItUpGroup = ko.observable(false);
    self.enablePickItUpGroup = ko.observable(true);
    self.showDiscardBtn = ko.observable(false);
    self.enableDiscardBtn = ko.observable(true);
    self.dealer = ko.observable(' ');
    self.isMyTurn = ko.observable(false);
    self.alone = ko.observable();
    self.cards = ko.observableArray();
    self.sortCardsCompareFn = function(a,b){ return a.suit === b.suit ? (a.rank == b.rank ? 0 : a.rank < b.rank ? -1 : 1) : a.suit < b.suit ? -1 : 1; };
    self.sortedCards = ko.pureComputed(function(){
      return self.cards.slice().sort(self.sortCardsCompareFn);
    });
    self.iamDealer = false;
    
    // This will be called on every heartbeat in most states of gameController.
    // If there is no deal, there is really nothing for this viewmodel to do.
    self.update = function(gameData, dealID) {
      if (!gameData.GameStartDate || !dealID) return;
      
      self.iamDealer = self.myPosition == gameData.Dealer;
      self.gameData = new gameModel(gameData);
      self.trump = self.gameData.OpponentTrump || self.gameData.OrganizerTrump;
      self.dealID = dealID;
      self.pickingItUp = self.gameData.CardFaceUp.length > 2 && self.gameData.CardFaceUp[2] == 'U' && self.iamDealer;
      self.discarded = self.gameData.CardFaceUp.length > 2 && self.gameData.CardFaceUp[2] == 'S' && self.iamDealer;
      
      if (self.myPosition == gameData.Dealer) {
        self.dealer('D');
      } else {
        self.dealer(' ');
      }
      
      self.isMyTurn(self.myPosition == gameData.Turn);
      
      var getmycards = false;
      var actaccordingtorules = false;
      
      if (self.dealID != self.previousDealID) {
        self.previousDealID = self.dealID;
        getmycards = true;
      }
      
      if (self.trump != self.previousTrump) {
        self.previousTrump = self.trump;
        getmycards = true;
      }
      
      if (gameData.CardFaceUp != self.previousCardFaceUp) {
        self.previousCardFaceUp = gameData.CardFaceUp;
        getmycards = true;
        actaccordingtorules = true;
      }
      
      if (getmycards || self.cards().length == 0) {
        self.getMyCards();
      }
      
      if (self.previousTurn != gameData.Turn) {
        self.previousTurn = gameData.Turn;
        if (self.isMyTurn()) {
          actaccordingtorules = true;
        } else {
          self.hideButtons();
        }
      }
      
      if (actaccordingtorules) {
        self.actAccordingToRules();
      }
    };
    
    self.getSuitOrder = function(c) {
      if (self.trump) {
        var order = 5;
        if (c[0] == 'J') {
          switch(self.trump) {
            case 'D':
            case 'H':
              if (c[1] == 'D' || c[1] == 'H') {
                order = 1;
              } else {
                switch(self.trump) {
                  case 'H':
                    order = c[1] == 'C' ? 4 : 2;
                    break;
                  case 'D':
                    order = c[1] == 'C' ? 4 : 3;
                    break;
                }
              }
              break;
            case 'S':
            case 'C':
              if (c[1] == 'S' || c[1] == 'C') {
                order = 1;
              } else {
                switch(self.trump) {
                  case 'S':
                    order = c[1] == 'D' ? 3 : 2;
                    break;
                  case 'C':
                    order = c[1] == 'D' ? 4 : 2;
                    break;
                }
              }
              break;
          }
        } else {
          switch(self.trump) {
            case 'D':
              order = c[1] == 'D' ? 1 : c[1] == 'C' ? 4 : c[1] == 'H' ? 2 : 3;
              break;
            case 'H':
              order = c[1] == 'D' ? 3 : c[1] == 'C' ? 4 : c[1] == 'H' ? 1 : 2;
              break;
            case 'S':
              order = c[1] == 'D' ? 3 : c[1] == 'C' ? 4 : c[1] == 'H' ? 2 : 1;
              break;
            case 'C':
              order = c[1] == 'D' ? 4 : c[1] == 'C' ? 1 : c[1] == 'H' ? 2 : 3;
              break;
          }
        }
        return order;
      } else {
        var s = c[1];
        return s == 'D' ? 1 : s == 'C' ? 2 : s == 'H' ? 3 : 4;
      }
    };
    
    self.getRank = function(c) {
      if (self.trump && c[0] == 'J') {
        var order = 15;
        switch(self.trump) {
          case 'D':
            order = c[1] == 'D' ? 1 : c[1] == 'H' ? 2 : 12;
            break;
          case 'H':
            order = c[1] == 'D' ? 2 : c[1] == 'H' ? 1 : 12;
            break;
          case 'S':
            order = c[1] == 'S' ? 1 : c[1] == 'C' ? 2 : 12;
            break;
          case 'C':
            order = c[1] == 'S' ? 2 : c[1] == 'C' ? 1 : 12;
            break;
        }
        return order;
      } else {
        var r = c[0];
        return r == '9' ? 14 : r == '1' ? 13 : r == 'J' ? 12 : r == 'Q' ? 11 :r == 'K' ? 10 : 9;
      }
    };

    self.selectCard = function(card){
      if ((self.isMyTurn() && self.trump) || self.pickingItUp) {
        self.cards().forEach(function(c){ 
          if (c.id == card.id && c.isPlayable()) {
            c.isSelected(!c.isSelected());
          } else {
            c.isSelected(false);
          }
        });
      }
    };
    
    self.getCardObject = function(c) {
      var o = {
        id: c, 
        url: app.getCardURL(c),
        suit: self.getSuitOrder(c),
        rank: self.getRank(c),
        isPlayable: ko.observable(true),
        isSelected: ko.observable(false)
      };
      
      return o;
    };
    
    self.getCardObjectForScoring = function(c,p) {
      var o = {
        id: c,         // card played
        positionID: p, // who played it
        suit: self.getSuitOrder(c),
        rank: self.getRank(c),
      };
      
      return o;
    };
    
    self.getWinnerOfHand = function() {
      var cards = [];
      
      cards.push(self.getCardObjectForScoring(self.gameData.PO, 'O'));
      cards.push(self.getCardObjectForScoring(self.gameData.PP, 'P'));
      cards.push(self.getCardObjectForScoring(self.gameData.PL, 'L'));
      cards.push(self.getCardObjectForScoring(self.gameData.PR, 'R'));
      cards.sort(self.sortCardsCompareFn);
      
      return cards[0].positionID;
    };
    
    self.getNewScore = function(winner) {
      var newScore = {
        organizerScore: Number(self.gameData.OrganizerScore),
        organizerTricks: Number(self.gameData.OrganizerTricks),
        opponentScore: Number(self.gameData.OpponentScore),
        opponentTricks: Number(self.gameData.OpponentTricks),
      };
      
      if (winner == 'O' || winner == 'P') {
        newScore.organizerTricks += 1;
      } else {
        newScore.opponentTricks += 1;
      }
      
      if (newScore.organizerTricks + newScore.opponentTricks == 5) {
        var organizerScoreThisHand = 0;
        var opponentScoreThisHand = 0;
        
        if (self.gameData.OrganizerTrump) {
          if (newScore.organizerTricks == 3 || newScore.organizerTricks == 4) {
            organizerScoreThisHand = 1;
          } else if (newScore.organizerTricks == 5) {
            if (self.gameData.CardFaceUp.length == 5) {
              organizerScoreThisHand = 4;
            } else {
              organizerScoreThisHand = 2;
            }
          }
          
          if (newScore.opponentTricks >= 3) {
            opponentScoreThisHand = 2;
          }
        } else {
          if (newScore.opponentTricks == 3 || newScore.opponentTricks == 4) {
            opponentScoreThisHand = 1;
          } else if (newScore.opponentTricks == 5) {
            if (self.gameData.CardFaceUp.length == 5) {
              opponentScoreThisHand = 4;
            } else {
              opponentScoreThisHand = 2;
            }
          }
          
          if (newScore.organizerTricks >= 3) {
            organizerScoreThisHand = 2;
          }
        }

        newScore.organizerScore += organizerScoreThisHand;
        newScore.opponentScore += opponentScoreThisHand;
        newScore.organizerTricks = 0;
        newScore.opponentTricks = 0;
      }
      
      return newScore;
    };
    
    self.getMyCards = function() {
      console.log('getMyCards()');
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
              
              if (self.pickingItUp) {
                c.push(self.getCardObject(self.gameData.CardFaceUp.substr(0,2)));
              }
              
              self.cards(c);
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
            console.log('Could not parse response from setNextTurn. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };
    
    self.getAllOtherCards = function(){
      switch(self.myPosition){
        case 'O':
          return self.gameData.PP + self.gameData.PL + self.gameData.PR;
        case 'P':
          return self.gameData.PO + self.gameData.PL + self.gameData.PR;
        case 'L':
          return self.gameData.PO + self.gameData.PP + self.gameData.PR;
        case 'R':
          return self.gameData.PO + self.gameData.PP + self.gameData.PL;
        default:
          return '';
      }
    };
    
    self.getSelectedCard = function(){
      var card = '';
      self.cards().forEach(function(c){
        if (c.isSelected()) {
          card = c.id;
        }
      });
      return card;
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
    
    self.pickItUpApi = function() {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.myPosition;
      pd.alone = self.alone();
      
      $.ajax({
        method: 'POST',
        url: 'api/pickItUp.php',
        data: pd,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            }
          } catch (error) {
            console.log('Could not parse response from pickItUp. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };
    
    self.pass = function() {
      self.enablePassBtn(false);
      self.showPickItUpGroup(false);
      if (self.iamDealer && self.gameData.CardFaceUp.length == 2) {
        self.declineCard();
      } else {
        self.setNextTurn();
      }
      // the gameController pops up the bid dialog when necessary.
    };

    self.playCard = function(cardID){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.myPosition;
      pd.cardID = cardID;
      
      $.ajax({
        method: 'POST',
        url: 'api/playCard.php',
        data: pd,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            } else {
              self.getMyCards(false);
              if (self.getAllOtherCards().length < 6) {
                self.setNextTurn();
              }
              // when 4 cards have been played, the game controller will score the hand and set Lead and Turn.
            }
          } catch (error) {
            console.log('Could not parse response from playCard. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };
    
    self.discardCard = function(cardID){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.myPosition;
      pd.cardID = cardID;
      
      $.ajax({
        method: 'POST',
        url: 'api/discardCard.php',
        data: pd,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            } else {
              self.getMyCards(false);
            }
          } catch (error) {
            console.log('Could not parse response from discardCard. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };
    
    self.play = function() {
      self.enablePlayBtn(false);
      var cardID = self.cards().length == 1 ?  self.cards()[0].id : self.getSelectedCard();
      if (cardID) {
        self.playCard(cardID);
      }
    }
    
    self.discard = function() {
      var cardID = self.getSelectedCard();
      if (cardID) {
        self.discardCard(cardID);
      }
    }
    
    self.pickItUp = function() {
      self.enablePickItUpGroup(false);
      self.showPassBtn(false);
      self.pickItUpApi();
    }
    
    self.enableBid = function() {
      self.showPassBtn(true);
      self.showPickItUpGroup(true);
    };
    
    self.hideButtons = function() {
      self.showPassBtn(false);
      self.showPlayBtn(false);
      self.showPickItUpGroup(false);
      self.showDiscardBtn(false);
      
      self.enablePassBtn(true);
      self.enablePickItUpGroup(true);
      self.enablePlayBtn(true);
    };
    
    self.actAccordingToRules = function() {
      console.log('actAccordingToRules()');

      self.hideButtons();
      
      if (self.gameData.CardFaceUp.length == 2 && self.isMyTurn()) {
        self.enableBid();
      }
      
      if (self.trump && self.isMyTurn()) {
        self.showPlayBtn(true);
      }
      
      if (self.pickingItUp) {
        self.showDiscardBtn(true);
      }
    };
    
    self.initialize = function(selfPosition, gameData){
      self.myPosition = selfPosition;
    };
  }
  
</script>