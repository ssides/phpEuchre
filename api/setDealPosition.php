<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getNextTurn.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName]) && isset($_POST['position'])) {
      
      $errorMsg = "";
      $gameID = $_POST['gameID'];
      $position = $_POST['position'];
      $isFirst = isset($_POST['isFirst']);
      
      $turn = getNextTurn($position);
      $sql = "";
      
      if ($isFirst) {
        $sql = "update `Game` set `FirstDealPosition`='{$position}',`Dealer`='{$position}', `Turn`='{$turn}', `CardFaceUp`=null where `ID`='{$gameID}'";
      } else {
        $sql = "update `Game` set `Dealer`='{$position}', `Turn`='{$turn}', `CardFaceUp`=null where `ID`='{$gameID}'";
      }
      
      mysqli_query($connection, "START TRANSACTION;");
      $result = mysqli_query($connection, $sql);
      if ($result === false) {
        $errorMsg = mysqli_error($connection);
        mysqli_query($connection, "ROLLBACK;");
      } else {
        $errorMsg = 'OK';
        mysqli_query($connection, "COMMIT;");
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