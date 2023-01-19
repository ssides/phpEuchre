<?php include_once('../../../config/config.php'); ?>

<script type="text/javascript">

  function debugLog(data) {
    this.dealID = data.DealID || '';
    this.gameControllerState = data.GameControllerState || '';
    this.insertDate = data.InsertDate || '';
    this.message = data.Message || '';
    this.opponentScore = data.OpponentScore || '';
    this.opponentTricks = data.OpponentTricks || '';
    this.organizerScore = data.OrganizerScore || '';
    this.organizerTricks = data.OrganizerTricks || '';
    this.positionID = data.PositionID || '';
  }

  function debugLogViewModel() {
    var self = this;
    
    self.log = ko.observableArray();
    self.gameID = ko.observable();
    
    self.getScores = function() {
      var postData = { 
        <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'" ?>,
        gameID: self.gameID()
        };
      $.ajax({
        method: 'POST',
        url: '../api/reports/getDebugLog.php',
        data: postData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            } else {
              var l = [];
              data.Log.forEach(function(i){
                l.push(new debugLog(i));
              });
              self.log(l);
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

    
  }    
  
  $(function () {
    ko.applyBindings(new debugLogViewModel());
  });
</script>
