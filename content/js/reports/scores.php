<?php include_once('../../../config/config.php'); ?>

<script type="text/javascript">

  function score(data) {
    this.organizerTrump = data.OrganizerTrump || '';
    this.opponentTrump = data.OpponentTrump || '';
    this.lead = data.Lead || '';
    this.cardO = data.CardO || '';
    this.cardL = data.CardL || '';
    this.cardP = data.CardP || '';
    this.cardR = data.CardR || '';
    this.organizerScore = data.OrganizerScore || '';
    this.opponentScore = data.OpponentScore || '';
    this.organizerTricks = data.OrganizerTricks || '';
    this.opponentTricks = data.OpponentTricks || '';
    this.cardFaceUp = data.CardFaceUp || '';
    this.dealer = data.Dealer || '';
    this.dealID = data.DealID || '';
  }

  function scoresViewModel() {
    var self = this;
    
    self.scores = ko.observableArray();
    self.gameID = ko.observable();
    
    self.getScores = function() {
      var postData = { 
        <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'" ?>,
        gameID: self.gameID().trim()
        };
      $.ajax({
        method: 'POST',
        url: '../api/reports/getScores.php',
        data: postData,
        success: function (response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            } else {
              var s = [];
              data.Scores.forEach(function(i){
                s.push(new score(i));
              });
              self.scores(s);
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
    ko.applyBindings(new scoresViewModel());
  });
</script>
