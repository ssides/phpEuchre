<?php include_once('../../config/config.php'); ?>

<script type="text/javascript">

  function game(gameID, organizerName, position) {
    this.gameID = gameID;
    this.organizerName = organizerName;
    this.position = position;
  }

  function dashboardViewModel() {
    var self = this;
    
    self.invitationTimer = null;
    self.invitations = ko.observableArray([]);
    self.rejoinTimer = null;
    self.rejoinGames = ko.observableArray([]);

    self.getInvitations = function() {
      var postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'" ?> };
      $.ajax({
        method: 'POST',
        url: 'api/getInvitations.php',
        data: postData,
        success: function (response) {
          console.log('received getInvitations response');
          let data = JSON.parse(response);
          if (data.length > 0) {
            self.invitations([]);
            // stop the timer and wait for the player to respond.
            clearInterval(self.invitationTimer);
            data.forEach(function(i){
              self.invitations.push(new game(i[0], i[1], i[2]));
            });
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          console.log(error);
        }
      });
    };

    self.getReJoinGames = function(){
      var postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'" ?> };
      $.ajax({
        method: 'POST',
        url: 'api/getRejoinableGames.php',
        data: postData,
        success: function (response) {
          console.log('received getRejoinableGames response');
          let data = JSON.parse(response);
          if (data.length > 0) {
            self.rejoinGames([]);
            // stop the timer and wait for the player to respond.
            clearInterval(self.rejoinTimer);
            data.forEach(function(i){
              self.rejoinGames.push(new game(i[0], i[1], i[2]));
            });
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          console.log(error);
        }
      });
    };
    
    self.initialize = function() {
      console.log('start timer invitationTimer.');
      self.invitationTimer = setInterval(self.getInvitations, 1000);
      // start another timer to get games from the last three days 
      // that can be rejoined.
      // everything needs to be stored in the database.  I need a Play table
      // that joins to Game. who is the dealer? what is trump?
      console.log('start timer rejoinTimer.');
      self.rejoinTimer = setInterval(self.getReJoinGames, 1000);
      
      $('.uxRefreshInvites').click(function(){ self.getInvitations(); });
      $('.uxRefreshReJoins').click(function(){ self.getReJoinGames(); });

    }
    
    self.initialize();
  }    
  
  $(function () {
    ko.applyBindings(new dashboardViewModel());
  });
</script>
