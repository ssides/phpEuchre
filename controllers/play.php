<?php
  include('config/db.php');
  include_once('config/config.php');
  
    if (empty($_COOKIE[$cookieName]) || empty($_SESSION['gameID'])) {
      header('Location: index.php');
    } else {
      $gameID = $_SESSION['gameID'];
      // I may not need this controller. 
      // This is only needed if the user wants to redirect.
      // All actions will be through api and ko.  The start
      // link is already on the nav bar.
    }

?>