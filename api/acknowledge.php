<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  
  // As "position" I am acknowledge that I've seen the Jack or that Scoring is in progress.
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName]) && isset($_POST['position'])) {
      
      $errorMsg = "";
      $gameID = $_POST['gameID'];
      $position = $_POST['position'];
      
      mysqli_query($connection, "START TRANSACTION;");

      $sql = "update `Game` set `AC{$position}`='A' where `ID`='{$gameID}'";
      $result = mysqli_query($connection, $sql);
      if ($result === false) {
        $errorMsg = mysqli_error($connection);
        mysqli_query($connection, "ROLLBACK;");
      } else {
        mysqli_query($connection, "COMMIT;");
        $errorMsg = 'OK';
      }
      
      http_response_code(200);
      echo $errorMsg;
      
    } else {
      echo "ID or position invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>