<?php include_once('../../config/config.php'); ?>

<script type="text/javascript">

  function game(gameID, organizerName, position) {
    this.gameID = gameID;
    this.organizerName = organizerName;
    this.position = position;
  }

  function dashboardViewModel() {
    var self = this;
    
    self.games = ko.observableArray();

    self.getInvitations = function() {
      var postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'" ?> };
      $.ajax({
        method: 'POST',
        url: 'api/getInvitations.php',
        data: postData,
        success: function (response) {
          let data = JSON.parse(response);
          data.forEach(function(i){
            self.games.push(new game(i[0], i[1], i[2]));
          });
        },
        error: function (xhr, status, error) {
          console.log(error);
        }
      });
    };

    self.initialize = function() {
      setInterval(self.getInvitations, 7000);
    }
    
    self.initialize();
  }    
  
  $(function () {
    ko.applyBindings(new dashboardViewModel());
  });
</script>
