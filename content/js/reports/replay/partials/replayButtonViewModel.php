<script type="text/javascript">

  function replayButtonViewModel() {
    var self = this;

    self.showReplayButton = ko.observable(true);
    
    self.setReplayButtonVisibility = function(v) {
      self.showReplayButton(v);
    }
    
    self.replay = function() {
      window.location.href = 'reports/replay-from-game.php';
    };

  }

</script>