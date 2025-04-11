<script type="text/javascript">

  function whatsTrumpViewModel() {
    var self = this;

    self.trumpURL = ko.observable('');
    self.isLoaner = ko.observable(false);
    self.playOnce = [];
    self.playOnceLength = 0;
    
    self.update = function(gameData) {
      var trump = '';
      
      if (gameData.CardFaceUp.length > 2 && gameData.CardFaceUp[2] == 'U') { // if picking it up
        trump = gameData.CardFaceUp[1];
        self.trumpURL(app.getCardURL(trump));
      } else {
        if (gameData.OrganizerTrump) {
          trump = gameData.OrganizerTrump;
          self.trumpURL(app.getCardURL(trump));
        } else if (gameData.OpponentTrump) {
          trump = gameData.OpponentTrump;
          self.trumpURL(app.getCardURL(trump));
        } else {
          trump = '';
          self.trumpURL(trump);
          self.playOnce = [];
        }
      }
      
      self.isLoaner(gameData.CardFaceUp.length == 5);
      self.playLoanerSound(trump, "loaner");
      
      if (trump && gameData.CardFaceUp.length > 2 && gameData.CardFaceUp[2] != 'U' && gameData.CardFaceUp[2] != 'S') {
        self.playTrumpSound(trump);
      }
    };
    
    self.playTrumpSound = function(trump){
      if (!self.playOnce.includes(trump)) {
        self.playOnce.push(trump);
        app.soundQueue.push(app.sounds[trump]);
      }
    };

    self.playLoanerSound = function(trump, loaner){
      if (trump && self.isLoaner()) {
        if (!self.playOnce.includes(loaner)) {
          self.playOnce.push(loaner);
          app.soundQueue.push(app.sounds[loaner]);
        }
      }
    };
  }

</script>