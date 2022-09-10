<?php
  include('config/db.php');
  include_once('config/config.php');
  
    if (empty($_COOKIE[$cookieName]) || empty($_SESSION['gameID'])) {
      header('Location: index.php');
    } else {
      $gameID = $_SESSION['gameID'];
      // I may not need this controller.d
    }

?>