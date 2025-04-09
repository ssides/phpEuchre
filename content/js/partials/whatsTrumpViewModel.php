<script type="text/javascript">

  function whatsTrumpViewModel() {
    var self = this;

    self.trumpURL = ko.observable('');
    self.isLoaner = ko.observable(false);
    
    self.update = function(gameData) {
      if (gameData.CardFaceUp.length > 2 && gameData.CardFaceUp[2] == 'U') { // if picking it up
        self.trumpURL(app.getCardURL(gameData.CardFaceUp[1]));
      } else {
        if (gameData.OrganizerTrump) {
          self.trumpURL(app.getCardURL(gameData.OrganizerTrump));
        } else if (gameData.OpponentTrump) {
          self.trumpURL(app.getCardURL(gameData.OpponentTrump));
        } else {
          self.trumpURL('');
        }
      }
      self.isLoaner(gameData.CardFaceUp.length == 5);
    };
    
  }

</script>