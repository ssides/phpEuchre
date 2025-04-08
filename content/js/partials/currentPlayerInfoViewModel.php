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
    self.previousDealID = '';
    self.previousTurn = '';
    self.previousCardFaceUp = '';
    self.previousCards = '';
    self.pickingItUp = false;
    self.bidModalIsVisible = false;
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
    self.iamSkipped = ko.observable(false);
    self.obsAlone = ko.observable();
    self.trumpURL = ko.observable('');
    self.cards = ko.observableArray();
    self.sortedCards = ko.pureComputed(function(){
      return self.cards.slice().sort(cardSort.sortCardsCompareFn);
    });
    self.iamDealer = false;
    
    // This will be called on every heartbeat in most states of gameController.
    // If there is no deal, there is really nothing for this viewmodel to do.
    // I wanted to keep all the rules in one place, so the game controller can
    // call these: getWinnerOfHand() and getNewScore(winner).  So make sure
    // self.gameData is always up to date.
    // todo: make sure self.initialize() is called before self.update()!
    self.update = function(game) {
      self.gameData = new gameModel(game);
      if (!self.myPosition || !self.gameData.GameStartDate || !self.gameData.DealID || self.gameData.allCardsHaveBeenPlayed() || self.gameData.ScoringInProgress) return;
      
      self.iamDealer = self.myPosition == self.gameData.Dealer;
      self.trump = self.gameData.OpponentTrump || self.gameData.OrganizerTrump;
      self.pickingItUp = self.gameData.CardFaceUp.length > 2 && self.gameData.CardFaceUp[2] == 'U' && self.iamDealer;
      
      self.setTrumpIcon();
      
      if (self.myPosition == self.gameData.Dealer) {
        self.dealer('D');
      } else {
        self.dealer(' ');
      }
      
      self.isMyTurn(self.gameData.CardFaceUp[2] == 'U' ? (self.myPosition == self.gameData.Dealer ? true : false) : self.myPosition == self.gameData.Turn);
      self.iamSkipped(self.gameData.CardFaceUp.length > 4 && self.gameData.CardFaceUp[4] == self.myPosition);
      
      var updateReason = '';
      
      if (self.gameData.DealID != self.previousDealID) {
        self.previousDealID = self.gameData.DealID;
        updateReason += 'D';  // new 'D'eal
      }
      
      if (self.trump != self.previousTrump) {
        self.previousTrump = self.trump;
        updateReason += 'T';  // new 'T'rump
      }
      
      if (self.gameData.CardFaceUp != self.previousCardFaceUp) {
        self.previousCardFaceUp = self.gameData.CardFaceUp;
        updateReason += 'U'; // new CardFace'U'p
      }
      
      if (self.gameData.getAllCards() != self.previousCards) {
        self.previousCards = self.gameData.getAllCards();
        updateReason += 'C'; // 'C'ards played.
      }
      
      if (self.previousTurn != self.gameData.Turn) {
        self.previousTurn = self.gameData.Turn;
        updateReason += 'R'; // change in tu'R'n
      }
      
      if (updateReason.length > 0) {
        self.actAccordingToRules(updateReason);
      }

      if (updateReason.length > 0 && self.iamSkipped() === false) {
        $.when(self.getMyCards(updateReason)).done(function(){
          if (self.isMyTurn() && !self.gameData.preScoring()) {
            if (self.trump && self.gameData.Lead) {
              self.markNotPlayable(self.cards());
            }
            self.showPlayBtn(self.trump && self.cards().length > 0 );
          } else if (!self.isMyTurn() && updateReason.indexOf('R') > 0){
            self.markAllCardsPlayable(self.cards());
          }
        });
      } else if (updateReason.length > 0 && self.iamSkipped() === true) {
        self.cards([]);
      }
    };

    // -- helper functions --
    self.setTrumpIcon = function() {
      if (self.trump && self.gameData.CardFaceUp.length > 3 && self.gameData.CardFaceUp[3] == self.myPosition) {
        if (self.myPosition == 'O' || self.myPosition == 'P') {
          if (self.gameData.OrganizerTrump) {
            self.trumpURL(app.getCardURL(self.gameData.OrganizerTrump));
          }
        } else {
          if (self.gameData.OpponentTrump) {
            self.trumpURL(app.getCardURL(self.gameData.OpponentTrump));
          }
        }
      } else {
        self.trumpURL('');
      }
    };
    
    self.getSuit = function(c) {
      if (c[0] == 'J') {
        switch (self.trump) {
          case 'H':
          case 'D':
            return c[1] == 'H' || c[1] == 'D' ? self.trump : c[1]; 
          case 'S':
          case 'C':
            return c[1] == 'S' || c[1] == 'C' ? self.trump : c[1]; 
        }
      } else {
        return c[1];
      }
    };
    
    self.followsSuit = function(lead, card) {
      var leadSuit = self.getSuit(lead);
      var cardSuit = self.getSuit(card);
      return (leadSuit == cardSuit) || (cardSuit == self.trump);
    };
    
    self.getLeadCard = function(){
      switch (self.gameData.Lead) {
        case 'O':
          return self.gameData.PO;
        case 'P':
          return self.gameData.PP;
        case 'L':
          return self.gameData.PL;
        case 'R':
          return self.gameData.PR;
      }
    };
    
    self.markNotPlayable = function(cards) {
      if (self.gameData.Lead) {
        var inSuitCount = 0;
        var leadSuit = self.getSuit(self.getLeadCard());
        
        cards.forEach(function(c){
          var cardSuit = self.getSuit(c.id);
          if (cardSuit == leadSuit)
            inSuitCount += 1;
        });
        
        if (inSuitCount > 0) {
          cards.forEach(function(c){
            var cardSuit = self.getSuit(c.id);
            if (cardSuit != leadSuit) {
              if (c.isPlayable()) {
                c.isPlayable(false);
              }
            }
          });
        }
      }
    }
    
    self.markAllCardsPlayable = function(cards) {
      cards.forEach(function(c){
        c.isPlayable(true);
      });
    }
    
    self.getMyCards = function(reason) {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.myPosition;
      
      return $.ajax({
        method: 'POST',
        url: 'api/getMyCards.php',
        data: pd,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            } else {
              var c = [];
              
              if (data.Cards.CardID1.length == 2)
                c.push(cardSort.getCardObject(self.trump, data.Cards.CardID1.substr(0,2)));
              if (data.Cards.CardID2.length == 2)
                c.push(cardSort.getCardObject(self.trump, data.Cards.CardID2.substr(0,2)));
              if (data.Cards.CardID3.length == 2)
                c.push(cardSort.getCardObject(self.trump, data.Cards.CardID3.substr(0,2)));
              if (data.Cards.CardID4.length == 2)
                c.push(cardSort.getCardObject(self.trump, data.Cards.CardID4.substr(0,2)));
              if (data.Cards.CardID5.length == 2)
                c.push(cardSort.getCardObject(self.trump, data.Cards.CardID5.substr(0,2)));
              
              if (self.pickingItUp) {
                var card = cardSort.getCardObject(self.trump, self.gameData.CardFaceUp.substr(0,2));
                card.isPlayable(false);
                c.push(card);
              }
              
              self.cards(c);
            }
          } catch (error) {
            app.errorVM.add('Could not parse response from getMyCards. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
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
              app.errorVM.add(data.ErrorMsg);
            }
          } catch (error) {
            app.errorVM.add('Could not parse response from setNextTurn. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
        }
      });
    };
    
    self.setNextTurnWithSkip = function() {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.myPosition;
      
      $.ajax({
        method: 'POST',
        url: 'api/setNextTurnWithSkip.php',
        data: pd,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            }
          } catch (error) {
            app.errorVM.add('Could not parse response from setNextTurn. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
        }
      });
    };

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
      self.enableDiscardBtn(true);

      self.obsAlone(false);
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
              app.errorVM.add(data.ErrorMsg);
            }
          } catch (error) {
            app.errorVM.add('Could not parse response from declineCard. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
        }
      });
    };
    
    self.shouldAdvanceTurn = function() {
      var limit = self.gameData.CardFaceUp.length == 5 ? 4 : 6;
      return self.getAllOtherCards().length < limit;
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
              app.errorVM.add(data.ErrorMsg);
            } else {
              self.getMyCards(false);
              if (self.shouldAdvanceTurn()) {
                self.setNextTurnWithSkip();
              }
              // when 4 cards have been played, the game controller will score the hand and set Lead and Turn.
            }
          } catch (error) {
            app.errorVM.add('Could not parse response from playCard. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
        }
      });
    };
    
    
    self.pickItUpApi = function() {
      
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.myPosition;
      pd.alone = self.obsAlone();

      $.ajax({
        method: 'POST',
        url: 'api/pickItUp.php',
        data: pd,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              app.errorVM.add(data.ErrorMsg);
            }
          } catch (error) {
            app.errorVM.add('Could not parse response from pickItUp. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
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
              app.errorVM.add(data.ErrorMsg);
            } else {
              self.getMyCards(false);
            }
          } catch (error) {
            app.errorVM.add('Could not parse response from discardCard. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          app.errorVM.add(xhr.responseText);
        }
      });
    };
    
    
    // bound click events
    self.selectCard = function(card){
      if (((self.isMyTurn() && self.trump) || self.pickingItUp) && !self.gameData.preScoring()) {
        self.cards().forEach(function(c){ 
          if (c.id == card.id && c.isPlayable()) {
            c.isSelected(!c.isSelected());
          } else {
            c.isSelected(false);
          }
        });
      }
    };

    self.discard = function() {
      var cardID = self.getSelectedCard();
      if (cardID) {
        self.enableDiscardBtn(false);
        self.discardCard(cardID);
      }
    }
    
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

    self.play = function() {
      // console.log('play()');
      var cardID = self.cards().length == 1 ?  self.cards()[0].id : self.getSelectedCard();
      if (cardID) {
        self.enablePlayBtn(false);
        self.playCard(cardID);
      }
    }
    
    self.pickItUp = function() {
      self.enablePickItUpGroup(false);
      self.showPassBtn(false);
      self.pickItUpApi();
    }
    
    // update actions
    self.actAccordingToRules = function(reason) {
      self.hideButtons();
      
      if (self.gameData.CardFaceUp.length == 2 && self.isMyTurn()) {
        self.enableBid();
      }
      
      if (self.pickingItUp) {
        self.showDiscardBtn(true);
      }
    };
    
    // called externally
    self.getCardObjectForScoring = function(c,p) {
      var o = {
        id: c,         // card played
        positionID: p, // who played it
        suit: cardSort.getSuitOrder(self.trump, c),
        rank: cardSort.getRank(self.trump, c),
      };
      
      return o;
    };

    self.getWinnerOfHand = function() {
      var cards = [];
      var leadCard = self.getLeadCard();
      cards.push(self.getCardObjectForScoring(leadCard, self.gameData.Lead));
      
      if (self.gameData.Lead != 'O' && self.followsSuit(leadCard, self.gameData.PO))
        cards.push(self.getCardObjectForScoring(self.gameData.PO, 'O'));
      if (self.gameData.Lead != 'P' && self.followsSuit(leadCard, self.gameData.PP))
        cards.push(self.getCardObjectForScoring(self.gameData.PP, 'P'));
      if (self.gameData.Lead != 'L' && self.followsSuit(leadCard, self.gameData.PL))
        cards.push(self.getCardObjectForScoring(self.gameData.PL, 'L'));
      if (self.gameData.Lead != 'R' && self.followsSuit(leadCard, self.gameData.PR))
        cards.push(self.getCardObjectForScoring(self.gameData.PR, 'R'));
      
      cards.sort(cardSort.sortCardsCompareFn);
      
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

    // -- hotkeys --
    self.hotKeyCardsAvailableForPlay = function(){
      var count = 0;
      self.cards().forEach(function(c){
        if (c.isPlayable()) {
          count++;
        }
      });
      return count;
    };
    
    self.hotKeySelectPlayableCards = function(){
      self.cards().forEach(function(c){
        if (c.isPlayable()) {
          c.isSelected(true);
        }
      });
    };
    
    self.hotKeySelectOnlyCard = function(){
      if (self.hotKeyCardsAvailableForPlay() == 1 && self.isMyTurn()) {
        self.hotKeySelectPlayableCards();
      }
    };
    
    self.hotKeyPlayCard = function() {
      if (self.showPlayBtn() !== false) {
        $('#play').click();
      }
    };
    
    self.hotKeyPass = function(){
      if (self.showPassBtn() !== false) {
        $('#pass').click();
      }
    };
    
    self.hotKeySpace = function() {
      if (self.showPickItUpGroup() !== false) {
        self.obsAlone(!self.obsAlone());
      } else {
        self.hotKeySelectOnlyCard();
      }
    };
    
    self.hotKeyPickItUp = function() {
      if (self.enablePickItUpGroup() !== false) {
        $('#pickitup').click();
      }
    }
      
    self.bidModalShown = function(visible) {
      self.bidModalIsVisible = visible;
    };
    
    self.hotKeys = function(event) {
      if (self.bidModalIsVisible) return;
      
      if (event.code == 'Space' && event.ctrlKey === false && event.shiftKey === false) {
        self.hotKeySpace();
      } else if ((event.code == 'Enter' && event.ctrlKey === false && event.shiftKey === false) 
              || (event.code == 'NumpadEnter' && event.ctrlKey === false && event.shiftKey === false)) {
        self.hotKeyPlayCard();
      } else if (event.code == 'KeyX' && event.ctrlKey === false) {
        self.hotKeyPass();
      } else if (event.code == 'KeyP' && event.ctrlKey === false) {
        self.hotKeyPickItUp();
      }
      event.preventDefault();
    };

    self.initialize = function(selfPosition, game){
      self.gameData = new gameModel(game);
      self.myPosition = selfPosition;
      
      document.addEventListener('keyup', self.hotKeys);
    };

  }
  
</script>