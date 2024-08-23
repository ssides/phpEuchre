<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getNextTurn.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST['r'];
      $positionID = $_POST['positionID']; // who ordered it up.
      $alone = $_POST['alone'] == 'true'; 
      $cardFaceUp = '';
      
      $sql = "select `CardFaceUp` from `Game` where `ID`='{$gameID}'";

      $conn = mysqli_connect($hostname, $username, $password, $dbname);

      $results = mysqli_query($conn, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
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
        
        mysqli_query($conn, "START TRANSACTION;");
        $results = mysqli_query($conn, $sql);
        if ($results === false) {
          $response['ErrorMsg'] .= mysqli_error($conn);
          mysqli_query($conn, "ROLLBACK;");
        } else {
          mysqli_query($conn, "COMMIT;");
        }
        
      } else {
        $response['ErrorMsg'] .= "pickItUp: Invalid game state. CardFaceUp: {$cardFaceUp} ";
      }

      mysqli_close($conn);

      http_response_code(200);
      
      echo json_encode($response);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>