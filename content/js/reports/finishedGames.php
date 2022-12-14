<?php include_once('../../../config/config.php'); ?>

<script type="text/javascript">

  function finishedGame(data) {
    this.finishDate = data.GameFinishDate || '';
    this.organizer = data.OName || '';
    this.partner = data.PName || '';
    this.organizerScore = Number(data.OrganizerScore);
    this.left = data.LName || '';
    this.right = data.RName || '';
    this.opponentScore = Number(data.OpponentScore);
  }

  function gamesViewModel() {
    var self = this;
    
    self.finishedGames = ko.observableArray([]);
    self.cutoffDate = ko.observable();
    
    self.getFinishedGames = function() {
      var postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'" ?> };
      $.ajax({
        method: 'POST',
        url: '../api/reports/getFinishedGames.php',
        data: postData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            }
            if (data.Games.length > 0) {
              var g = [];
              data.Games.forEach(function(i){
                g.push(new finishedGame(i));
              });
              self.cutoffDate(data.CutoffDate);
              self.finishedGames(g);
            }
          } catch (error) {
            console.log('Error ' + ': ' + error.message || error);
            console.log(error.stack);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          console.log(error);
        }
      });
    };

    self.initialize = function() {
      self.getFinishedGames();
    }
    
    self.initialize();
  }    
  
  $(function () {
    ko.applyBindings(new gamesViewModel());
  });
</script>
