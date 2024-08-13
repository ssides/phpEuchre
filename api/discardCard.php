<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getHand.php');
  include('../svc/getNextTurn.php');
  include('../svc/goingAlone.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST[$cookieName];
      $positionID = $_POST['positionID'];
      $cardID = $_POST['cardID']; // card being discarded
      
      $dealer = '';
      $cardFaceUp = '';
      
      $conn =  mysqli_connect($hostname, $username, $password, $dbname);
      
      mysqli_query($conn, "START TRANSACTION;");
      
      $sql = "select `Dealer`,`CardFaceUp` from `Game` where `ID`='{$gameID}'";

      $results = mysqli_query($conn, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $dealer = is_null($row['Dealer']) ? '' : $row['Dealer'];
          $cardFaceUp = is_null($row['CardFaceUp']) ? '' : $row['CardFaceUp'];
        }
      }
      
      if (strlen($dealer) > 0 && $dealer == $positionID && strlen($cardFaceUp) > 3 && $cardFaceUp[2] == 'U') {
        $hand = getHand($conn, $gameID, $positionID);
        if (strlen($hand['ErrorMsg']) > 0) {
          $response['ErrorMsg'] .= $hand['ErrorMsg'];
        }

        $cardNumber = getCardNumber($hand, $cardID);
        
        if ($cardNumber == '0') {
          // I don't let them discard CardFaceUp.
          $response['ErrorMsg'] .= "Card not found '{$cardID}'. ";
        } else {
          // save the hand and set trump based on who ordered it up. Turn goes to left of dealer.
          $response['ErrorMsg'] .= saveHandSetTurn($conn, $hand, $cardNumber, $cardFaceUp, $gameID, $dealer);
        }
      } else {
        $response['ErrorMsg'] .= "discardCard: Wrong state error.";
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

  // save the hand and set trump based on who ordered it up.
  function saveHandSetTurn($conn, $hand, $cardNumber, $cardFaceUp, $gameID, $dealer) {
    $response = "";
    $cardID = substr($cardFaceUp,0,2);
    $trump = substr($cardFaceUp,1,1);
    $playerID = substr($cardFaceUp,3,1);
    $trumpColumn = $playerID == 'O' || $playerID == 'P' ? 'OrganizerTrump' : 'OpponentTrump';
    $turn = getNextTurn($dealer);

    if (getAlone($cardFaceUp)) {
      $skipped = getSkippedPosition($cardFaceUp[3]);
      if ($turn == $skipped) {
        $turn = getNextTurn($turn);
      }
    }
    
    $sql = "update `Play` set `CardID{$cardNumber}` = '{$cardID}' where `ID`='{$hand['PlayID']}'";
    $results = mysqli_query($conn, $sql);
    if ($results === false) {
      $response .= mysqli_error($conn);
    }
    
    $cardFaceUp = $cardID.'S'.substr($cardFaceUp,3);
    $sql = "update `Game` set `{$trumpColumn}` = '{$trump}',`CardFaceUp` = '{$cardFaceUp}',`Turn` = '{$turn}' where `ID`='{$gameID}'";
    $results = mysqli_query($conn, $sql);
    if ($results === false) {
      $response .= mysqli_error($conn);
    }
    
    return $response;
  }
?>