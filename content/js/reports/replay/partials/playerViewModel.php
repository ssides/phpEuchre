<script type="text/javascript">

  function playerViewModel() {
    var self = this;
    self.thumbnailURL = ko.observable('');
    self.trumpURL = ko.observable('');
    self.name = ko.observable('');
    self.cards = ko.observableArray();
    self.showLead = ko.observable(false);
    self.sortedCards = ko.pureComputed(function(){
      return self.cards.slice().sort(cardSort.sortCardsCompareFn);
    });
    self.dealer = ko.observable('');
    self.iamSkipped = ko.observable(false);
    
    self.getMyCards = function(position, hand) {
      self.cards([]);
      var trump = (hand.organizerTrump == '-' ? hand.opponentTrump : hand.organizerTrump);
      hand.tricks.forEach(function(t){
        switch(position) {
          case 'O': self.cards.push(cardSort.getCardObject(trump, t.cardO)); break;
          case 'P': self.cards.push(cardSort.getCardObject(trump, t.cardP)); break;
          case 'L': self.cards.push(cardSort.getCardObject(trump, t.cardL)); break;
          case 'R': self.cards.push(cardSort.getCardObject(trump, t.cardR)); break;
        }
      });
    };
    
    self.updateHand = function(position, hand) {
      self.dealer(hand.dealer == position ? 'D' : ' ');
      self.iamSkipped((hand.cardFaceUp.length > 4 ? position == hand.cardFaceUp[4] : false));
      self.getMyCards(position, hand);
      self.trumpURL('');
      switch(hand.cardFaceUp[3]) {
          case 'O': if (position == 'O') self.trumpURL(app.getCardURL(hand.organizerTrump)); break;
          case 'P': if (position == 'P') self.trumpURL(app.getCardURL(hand.organizerTrump)); break;
          case 'L': if (position == 'L') self.trumpURL(app.getCardURL(hand.opponentTrump)); break;
          case 'R': if (position == 'R') self.trumpURL(app.getCardURL(hand.opponentTrump)); break;
        }

    };
    
    self.updateName = function(url, name, isOrganizer){
      self.thumbnailURL(url);
      self.name((isOrganizer === true ? name + ' (Organizer)' : name));
    };
    
    self.getCard = function(c) {
      var card = null;
      
      for (const [index, v] of self.cards().entries()) {
        if (v.id == c) {
          card = v;
          break;
        }
      }
      
      return card;
    };
    
    self.playCard = function(c) {
      self.getCard(c).isPlayable(false);
    };
    
    self.updateLead = function(v) {
      self.showLead(v);
    }
    
    self.isSkipped = function() {
      return self.iamSkipped();
    }
  }

</script>