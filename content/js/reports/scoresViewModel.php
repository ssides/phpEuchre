<script type="text/javascript">

  function scoresViewModel() {
    var self = this;
    
    self.scores = ko.observableArray();
    self.gameID = ko.observable();
    self.timeout = 10; // five seconds
    self.scoresTimer = null;
    
    self.getScoresFinished = function() {
      self.timeout--;
      if (!scores.success && self.timeout === 0) {
        console.log('Could not get scores.');
      }
      if (scores.success || self.timeout === 0) {
        clearInterval(self.scoresTimer);
        if (scores.success) {
          self.scores(scores.scores);
        }
      }
    }
    
    self.getScores = function() {
      self.timeout = 10; // five seconds
      self.scoresTimer = setInterval(self.getScoresFinished, 500);
      scores.getScores(self.gameID().trim());
    };
  }
  
  $(function () {
    ko.applyBindings(new scoresViewModel());
  });
</script>
