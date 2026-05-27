<script type="text/javascript">

  function replayButtonViewModel() {
    var self = this;

    self.showReplayButton = ko.observable(true);
    
    self.setReplayButtonVisibility = function(v) {
      self.showReplayButton(v);
    }
    
    self.replay = function() {
      window.open('reports/replay-from-game.php', '_blank');
    };

  }

</script>