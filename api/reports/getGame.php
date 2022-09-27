<?php
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      $gameID = $_POST['gameID'];

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
  
?>