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
  
  scores = {};
  
  scores.scores = [];
  scores.success = false;
  
  scores.getScores = function(gameID) {
    scores.success = false;
    var postData = { 
      <?php echo 'r:'."'{$$a['r']}'" ?>,
      gameID: gameID
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
            scores.scores = [];
            data.Scores.forEach(function(i){
              scores.scores.push(new score(i));
            });
            scores.success = true;
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

</script>
