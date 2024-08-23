<?php include_once('../../config/config.php'); ?>

<script type="text/javascript">

  function game(gameID, organizerName, position) {
    this.gameID = gameID;
    this.organizerName = organizerName;
    this.position = position;
  }

  function endableGame(gameID, insertDate, organizerScore, opponentScore) {
    this.gameID = gameID;
    this.insertDate = insertDate;
    this.organizerScore = organizerScore;
    this.opponentScore = opponentScore;
  }

  function dashboardViewModel() {
    var self = this;
    
    self.invitationTimer = null;
    self.invitationInProgress = false;  // lets the $.ajax() call take more than a second to complete.
    self.invitations = ko.observableArray([]);
    
    self.rejoinTimer = null;
    self.rejoinInProgress = false;
    self.rejoinGames = ko.observableArray([]);

    self.endgameTimer = null;
    self.endgameInProgress = false;
    self.endgameGames = ko.observableArray([]);

    self.getInvitations = function() {
      if (!self.invitationInProgress) {
        self.invitationInProgress = true;
        var postData = { <?php echo 'r:'."'{$$a['r']}'" ?> };

        $.ajax({
          method: 'POST',
          url: 'api/getInvitations.php',
          data: postData,
          success: function (response) {
            let data = JSON.parse(response);
            if (data.length > 0) {
              self.invitations([]);
              // stop the timer for efficiency.  no need to keep looking for results.
              clearInterval(self.invitationTimer);
              data.forEach(function(i){
                self.invitations.push(new game(i[0], i[1], i[2]));
              });
            }
          },
          error: function (xhr, status, error) {
            console.log(xhr.responseText);
            console.log(error);
          },
          complete: function(){
            self.invitationInProgress = false;
          }
        });
      }
    };

    self.getRejoinableGames = function(){
      if (!self.rejoinInProgress) {
        self.rejoinInProgress = true;
        var postData = { <?php echo 'r:'."'{$$a['r']}'" ?> };
        $.ajax({
          method: 'POST',
          url: 'api/getRejoinableGames.php',
          data: postData,
          success: function (response) {
            let data = JSON.parse(response);
            if (data.length > 0) {
              self.rejoinGames([]);
              // stop the timer for efficiency.  no need to keep looking for results.
              clearInterval(self.rejoinTimer);
              data.forEach(function(i){
                self.rejoinGames.push(new game(i[0], i[1], i[2]));
              });
            }
          },
          error: function (xhr, status, error) {
            console.log(xhr.responseText);
            console.log(error);
          },
          complete: function(){
            self.rejoinInProgress = false;
          }
        });
      }
    };
    
    self.getEndgameGames = function(){
      if (!self.endgameInProgress) {
        self.endgameInProgress = true;
        var postData = { <?php echo 'r:'."'{$$a['r']}'" ?> };
        $.ajax({
          method: 'POST',
          url: 'api/getEndableGames.php',
          data: postData,
          success: function (response) {
            let data = JSON.parse(response);
            if (data.length > 0) {
              self.endgameGames([]);
              // stop the timer for efficiency.  no need to keep looking for results.
              clearInterval(self.endgameTimer);
              data.forEach(function(i){
                self.endgameGames.push(new endableGame(i[0], i[1], i[2], i[3]));
              });
            }
          },
          error: function (xhr, status, error) {
            console.log(xhr.responseText);
            console.log(error);
          },
          complete: function(){
            self.endgameInProgress = false;
          }
        });
      }
    }
    
    self.initialize = function() {
      self.invitationTimer = setInterval(self.getInvitations, 1000);
      // start another timer to get games from the last three days that can be rejoined.
      self.rejoinTimer = setInterval(self.getRejoinableGames, 1000);
      self.endgameTimer = setInterval(self.getEndgameGames, 1000);
    }
    
    self.initialize();
  }    
  
  $(function () {
    ko.applyBindings(new dashboardViewModel());
  });
</script>
