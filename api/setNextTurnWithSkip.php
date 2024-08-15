<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getNextTurn.php');
  include('../svc/goingAlone.php');

  // If someone ordered it up alone, skip that player's partner.
  // phpEuchre\api\chooseTrump.php has similar functionality, but those functions don't 
  // work in this context.
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST[$cookieName];
      $positionID = $_POST['positionID'];

      $cardFaceUp = "";
      
      $sql = "select `CardFaceUp`  from `Game`  where `ID`='{$gameID}'";
      
      $conn = mysqli_connect($hostname, $username, $password, $dbname);
      
      mysqli_query($conn, "START TRANSACTION;");
      
      $results = mysqli_query($conn, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $cardFaceUp = is_null($row['CardFaceUp']) ? '' : $row['CardFaceUp'];
        }
      }
      
      if (strlen($cardFaceUp) > 3 ) {
        $turn = getNextTurn($positionID);

        if (getAlone($cardFaceUp)) {
          $skipped = getSkippedPosition($cardFaceUp[3]);
          if ($turn == $skipped) {
            $turn = getNextTurn($turn);
          }
        }
        
        $sql = "update `Game` set `Turn` = '{$turn}' where `ID`='{$gameID}'";
           
        $results = mysqli_query($conn, $sql);
        if ($results === false) {
          $response['ErrorMsg'] .= mysqli_error($conn);
        }
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