<script type="text/javascript">

  function whatsTrumpViewModel() {
    var self = this;

    self.trumpURL = ko.observable('');
    
    self.updateHand = function(h) {
      if (h.organizerTrump == '-') {
        self.trumpURL(app.getCardURL(h.opponentTrump));
      } else {
        self.trumpURL(app.getCardURL(h.organizerTrump));
      }
    }
  }

</script>