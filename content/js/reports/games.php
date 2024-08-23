<?php include_once('../../../config/config.php'); ?>

<script type="text/javascript">

  function game(data) {
    this.gameID = data.GameID || '';
    this.gameStartDate = data.GameStartDate || '';
    this.gameFinishDate = data.GameFinishDate || '';
    this.oName = data.OName || '';
    this.pName = data.PName || '';
    this.lName = data.LName || '';
    this.rName = data.RName || '';
    this.oCanRejoin = data.OCanRejoin || '';
    this.pCanRejoin = data.PCanRejoin || '';
    this.lCanRejoin = data.LCanRejoin || '';
    this.rCanRejoin = data.RCanRejoin || '';
    this.cutoffDate = data.CutoffDate || '';
  }

  function gamesViewModel() {
    var self = this;
    
    self.games = ko.observableArray([]);

    self.getGames = function() {
      var postData = { <?php echo 'r:'."'{$$a['r']}'" ?> };
      $.ajax({
        method: 'POST',
        url: '../api/reports/getGames.php',
        data: postData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.length > 0) {
              var g = [];
              data.forEach(function(i){
                g.push(new game(i));
              });
              self.games(g);
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
      self.getGames();
    }
    
    self.initialize();
  }    
  
  $(function () {
    ko.applyBindings(new gamesViewModel());
  });
</script>
