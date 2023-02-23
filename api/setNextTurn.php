<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getNextTurn.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $gameID = $_POST['gameID'];
      $playerID = $_POST[$cookieName];
      $positionID = $_POST['positionID'];
      $response = array();
      $response['ErrorMsg'] = "";

      $turn = getNextTurn($positionID);
      
      $sql = "update `Game` set `Turn` = '{$turn}' where `ID`='{$gameID}'";
         
      mysqli_query($connection, "START TRANSACTION;");
      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] = mysqli_error($connection);
        mysqli_query($connection, "ROLLBACK;");
      } else {
        mysqli_query($connection, "COMMIT;");
      }

      http_response_code(200);
      
      echo json_encode($response);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>