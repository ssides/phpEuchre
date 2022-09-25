<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $errorMsg = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST[$cookieName];
      $dealerPosition = $_POST['dealerPosition'];
      
      $sql = 
        "update `Game` 
         where `ID`='{$gameID}'";
         
      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $errorMsg = mysqli_error($connection);
      } 

      http_response_code(200);
      if (strlen($errorMsg) > 0)
        echo $errorMsg;
      else  
        echo "OK";

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>