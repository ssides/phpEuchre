<script type="text/javascript">

  function scoreViewModel() {
    var self = this;
    self.label = ko.observable('');
    self.score = ko.observable(0);
    self.tricks = ko.observable('');

    self.updateName = function(position) {
      self.label(position);
    }

    self.zeroScore = function() {
      self.score(0);
      self.tricks('');
    };
    
    self.updateScore = function(hand) {
      if (self.label() == "Organizer") {
        self.score(hand.organizerScore);
      } else {
        self.score(hand.opponentScore);
      }
    }
    
    self.getTricks = function(t){
      var tricks = '';
      for(let i = 0; i < t; i++) {
        tricks += '&#149;&nbsp;';
      }
      return tricks;
    };

    self.updateTricks = function(trick) {
      if (self.label() == "Organizer") {
        self.tricks(self.getTricks(trick.organizerTricks));
      } else {
        self.tricks(self.getTricks(trick.opponentTricks));
      }
    }
  }

</script>