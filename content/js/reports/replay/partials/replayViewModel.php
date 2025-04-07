<script type="text/javascript">

  function replayViewModel() {
    var self = this;

    self.message = ko.observable('');  // e.g. 'no more hands to display'
    self.cardFaceUpMessage = ko.observable(''); // e.g. 'Dealer picked up'
    self.cardFaceUpURL = ko.observable('');
    self.handMessage = ko.observable(''); // e.g. 'Hand 1 of n'
    self.showCardFaceUpBlock = ko.observable(false);
    self.showPreviousHandButton = ko.observable(false);
    self.showNextHandButton = ko.observable(false);
    
    self.nextHand = function(){ self.message('not implemented'); };
    self.previousHand = function(){ self.message('not implemented'); };
    self.playCard = function(){ self.message('not implemented'); };
    self.playTrick = function(){ self.message('not implemented'); };
    
    self.setMessage = function(msg){
      self.message(msg);
    }; 
    
    self.setHandMessage = function(msg){
      self.handMessage(msg);
    };
    
    self.setCardFaceUp = function(h){
      switch(h.cardFaceUp[2]) {
        case 'S': self.cardFaceUpMessage('Dealer picked up'); break;
        case 'D': self.cardFaceUpMessage('Dealer declined'); break;
        case 'K': self.cardFaceUpMessage('Not used, partner of dealer took it alone'); break;
        default: self.cardFaceUpMessage('What is ' + h.cardFaceUp[2] + '?'); break;
      }
      self.cardFaceUpURL(app.getCardURL(h.cardFaceUp.slice(0, 2)));
    };
    
    self.updateHand = function(h, fromActiveGame) {
      self.setCardFaceUp(h);
      self.showCardFaceUpBlock(true);
      if (!fromActiveGame) {
        self.showPreviousHandButton(true);
        self.showNextHandButton(true);
      }
    };
    
    self.setCallbacks = function(nextHandCallback, previousHandCallback, playCardCallback, playTrickCallback){
      self.nextHand = nextHandCallback;
      self.previousHand = previousHandCallback;
      self.playCard = playCardCallback;
      self.playTrick = playTrickCallback;
    }
  }

</script>