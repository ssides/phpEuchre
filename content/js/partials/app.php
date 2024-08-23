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

  app.apiPostData = { <?php echo 'r:'."'{$$a['r']}'"
                   .",gameID:'{$_SESSION['gameID']}'"   ?>  };

  app.getCardURL = function(cardID){
      return app.appURL + 'content/images/cards/' + cardID + '.png';
    };
  
  app.gameControllerLog = <?php echo $gameControllerLog ? 'true' : 'false'; ?>;
  
  app.clearTableDelay = <?php echo $clearTableDelay; ?>;
  
  app.gameEnded = function () {
      window.location.href = 'dashboard.php';
    }
</script>