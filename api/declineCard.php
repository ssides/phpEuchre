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
      $positionID = $_POST['positionID'];
      $dealer = '';
      $cardFaceUp = '';
      
      $conn =  mysqli_connect($hostname, $username, $password, $dbname);
      
      mysqli_query($conn, "START TRANSACTION;");

      $sql = "select `Dealer`,`CardFaceUp`
        from `Game` 
        where `ID`='{$gameID}'";

      $results = mysqli_query($conn, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $dealer = is_null($row['Dealer']) ? '' : $row['Dealer'];
          $cardFaceUp = is_null($row['CardFaceUp']) ? '' : $row['CardFaceUp'];
        }
      }
      
      if (strlen($dealer) > 0 && strlen($cardFaceUp) > 0) {
        if ($dealer != $positionID) {
          $response['ErrorMsg'] .= "Invalid PositionID: Dealer: {$dealer} PositionID: {$positionID}. ";
        } else {
          $cardFaceUp .= 'D';
          $turn = getNextTurn($positionID);

          $sql = "update `Game` set `CardFaceUp` = '{$cardFaceUp}', `Turn` = '{$turn}' where `ID`='{$gameID}'";
          
          $results = mysqli_query($conn, $sql);
          if ($results === false) {
            $response['ErrorMsg'] .= mysqli_error($conn);
          }
        }
      } else {
        $response['ErrorMsg'] .= "declineCard: Invalid game state.";
      }

      if (strlen($response['ErrorMsg']) > 0) {
        mysqli_query($conn, "ROLLBACK;");
      } else {
        mysqli_query($conn, "COMMIT;");
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