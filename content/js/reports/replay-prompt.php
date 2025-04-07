<?php include_once('../../../config/config.php'); ?>

<script type="text/javascript">

  function replayPromptViewModel() {
    var self = this;
    
    self.gameID = ko.observable();
    
    self.setGameID = function() {
      var postData = { 
        gameID: self.gameID().trim()
        };
      $.ajax({
        method: 'POST',
        url: '../api/reports/setReplayGameID.php',
        data: postData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            }
            window.location.href = 'replay.php';
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
    
    self.gotoReplay = function() {
      self.setGameID();
      
    }
    
  }
  
  $(function () {
    ko.applyBindings(new replayPromptViewModel());
  });
</script>
