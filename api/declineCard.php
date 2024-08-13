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
      $positionID = $_POST['positionID'];
      $dealer = '';
      $cardFaceUp = '';
      
      $sql = "select `Dealer`,`CardFaceUp`
        from `Game` 
        where `ID`='{$gameID}'";

      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($connection);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $dealer = is_null($row['Dealer']) ? '' : $row['Dealer'];
          $cardFaceUp = is_null($row['CardFaceUp']) ? '' : $row['CardFaceUp'];
        }
      }
      
      if (strlen($dealer) > 0 && strlen($cardFaceUp) > 0) {
        if ($dealer != $positionID) {
          $response['ErrorMsg'] .= "Dealer: {$dealer} PositionID: {$positionID}. ";
        } else {
          $cardFaceUp .= 'D';
          $turn = getNextTurn($positionID);

          $sql = "update `Game` set `CardFaceUp` = '{$cardFaceUp}', `Turn` = '{$turn}' where `ID`='{$gameID}'";
          
          mysqli_query($connection, "START TRANSACTION;");
          $results = mysqli_query($connection, $sql);
          if ($results === false) {
            $response['ErrorMsg'] .= mysqli_error($connection);
            mysqli_query($connection, "ROLLBACK;");
          } else {
            mysqli_query($connection, "COMMIT;");
          }
        }
      } else {
        $response['ErrorMsg'] .= "declineCard: Wrong state error.";
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