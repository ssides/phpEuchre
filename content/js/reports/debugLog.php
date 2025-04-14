<script type="text/javascript">

  function debugLog(data) {
    this.dealID = data.DealID || '';
    this.playerID = data.PlayerID || '';
    this.gameControllerState = data.GameControllerState || '';
    this.insertDate = data.InsertDate || '';
    this.message = data.Message || '';
    this.opponentScore = data.OpponentScore || '';
    this.opponentTricks = data.OpponentTricks || '';
    this.organizerScore = data.OrganizerScore || '';
    this.organizerTricks = data.OrganizerTricks || '';
    this.positionID = data.PositionID || '';
    
    this.dealer = data.Dealer || '';
    this.turn = data.Turn || '';
    this.cardFaceUp = data.CardFaceUp || '';
    this.aco = data.ACO || '';
    this.acp = data.ACP || '';
    this.acl = data.ACL || '';
    this.acr = data.ACR || '';
    this.po = data.PO || '';
    this.pp = data.PP || '';
    this.pl = data.PL || '';
    this.pr = data.PR || '';
  }

  function debugLogViewModel() {
    var self = this;
    
    self.log = ko.observableArray();
    self.gameID = ko.observable();
    
    self.getLogData = function() {
      var postData = { 
        <?php echo 'r:'."'{$$a['r']}'" ?>,
        gameID: self.gameID().trim()
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
