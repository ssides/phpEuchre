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
      
      $gameID = $_POST['gameID'];
      $playerID = $_POST[$cookieName];
      $positionID = $_POST['positionID'];
      $response = array();
      $response['ErrorMsg'] = "";

      $cardFaceUp = "";
      
      $sql = "select `CardFaceUp`  from `Game`  where `ID`='{$gameID}'";

      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($connection);
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
           
        $results = mysqli_query($connection, $sql);
        if ($results === false) {
          $response['ErrorMsg'] = mysqli_error($connection);
        } 
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