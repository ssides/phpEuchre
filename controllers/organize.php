<?php  
    include_once('config/db.php');
    include_once('config/config.php');
    
    if (empty($_COOKIE[$cookieName]) || empty($_SESSION['gameID'])) {
      header('Location: index.php');
    } else {
      $gameID = $_SESSION['gameID'];
      if(isset($_POST['startGame'])) {
        // todo: set GameStartDate.
        header('Location: play.php');
      }
    }
?>