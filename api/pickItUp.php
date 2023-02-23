<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getNextTurn.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST[$cookieName];
      $positionID = $_POST['positionID']; // who ordered it up.
      $alone = $_POST['alone'] == 'true'; 
      $cardFaceUp = '';
      
      $sql = "select `CardFaceUp` from `Game` where `ID`='{$gameID}'";

      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($connection);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $cardFaceUp = is_null($row['CardFaceUp']) ? '' : $row['CardFaceUp'];
        }
      }
      
      if (strlen($cardFaceUp) == 2) {
        $cardFaceUp .= "U{$positionID}";
        if ($alone) {
          $cardFaceUp .= getPlayerSkipped($positionID);
        }
        $sql = "update `Game` set `CardFaceUp` = '{$cardFaceUp}' where `ID`='{$gameID}'";
        
        mysqli_query($connection, "START TRANSACTION;");
        $results = mysqli_query($connection, $sql);
        if ($results === false) {
          $response['ErrorMsg'] .= mysqli_error($connection);
          mysqli_query($connection, "ROLLBACK;");
        } else {
          mysqli_query($connection, "COMMIT;");
        }
        
      } else {
        $response['ErrorMsg'] .= "Wrong state error. CardFaceUp: {$cardFaceUp} ";
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