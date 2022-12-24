<script type="text/javascript">

  function whatsTrumpViewModel() {
    var self = this;

    self.trumpURL = ko.observable('');
    
    self.update = function(gameData) { 
      if (gameData.OrganizerTrump) {
        self.trumpURL(app.getCardURL(gameData.OrganizerTrump));
      } else if (gameData.OpponentTrump) {
        self.trumpURL(app.getCardURL(gameData.OpponentTrump));
      } else {
        self.trumpURL('');
      }
    };
    
  }

</script>