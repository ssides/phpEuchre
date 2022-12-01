<?php include_once('../../../config/config.php'); ?>

<script type="text/javascript">

  function game(data) {
    this.gameID = data.gameID || '';
  }

  function gamesViewModel() {
    var self = this;
    
    self.games = ko.observableArray([]);

    self.getGames = function() {
      var postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'" ?> };
      $.ajax({
        method: 'POST',
        url: '../api/reports/getGames.php',
        data: postData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.length > 0) {
              self.games([]);
              data.forEach(function(i){
                self.games.push(new game(i));
              });
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
