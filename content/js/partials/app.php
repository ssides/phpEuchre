<?php
  include_once('../../../config/config.php');
?>

<script type="text/javascript">

  app = {};
  
  app.times = {
    firstJackTime: 600,
    gameTime: 1000
  };
  
  app.positions = 'OPLR';
  app.appURL = '<?php echo $appUrl; ?>';

  app.state = {
    initialize: 0,
    selectFirstJack: 1,
    waitForAcknowledgments: 2,
    idle: 3,
    dealOrWaitForCardFaceUp: 4,
    waitForCardFaceUp: 5,
    waitForTrump: 6,
    waitForBidDialog: 7,
    waitForPlay:  8,
    scoreHand:  9,
    waitForScore: 10,
    clearBoard: 11,
    waitForDiscard: 12
  };

  app.apiPostData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'"
                   .",gameID:'{$_SESSION['gameID']}'"   ?>  };

  app.getCardURL = function(cardID){
      return app.appURL + 'content/images/cards/' + cardID + '.png';
    };
  
</script>