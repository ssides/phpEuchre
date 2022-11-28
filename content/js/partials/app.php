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
    waitForAcknowledgements: 2,
    deal: 3,
    chooseTrump: 4,
    idle: 5,
    dealOrWaitForCardFaceUp: 6,
    waitForCardFaceUp: 7,
    waitForMyTurn: 8
  };

  app.apiPostData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'"
                   .",gameID:'{$_SESSION['gameID']}'"   ?>  };

  app.getCardURL = function(cardID){
      return app.appURL + 'content/images/cards/' + cardID + '.png';
    };
  
</script>