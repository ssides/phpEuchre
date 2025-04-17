<script type="text/javascript">

  function whatsTrumpViewModel() {
    var self = this;

    self.trumpURL = ko.observable('');
    self.isLoner = ko.observable(false);
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
      
      self.isLoner(gameData.CardFaceUp.length == 5);
      self.playLonerSound(trump, "loner");
      
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

    self.playLonerSound = function(trump, loner){
      if (trump && self.isLoner()) {
        if (!self.playOnce.includes(loner)) {
          self.playOnce.push(loner);
          app.soundQueue.push(app.sounds[loner]);
        }
      }
    };
  }

</script>