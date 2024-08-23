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
      $positionID = $_POST['positionID'];
      $trumpID = $_POST['trumpID'];
      $alone = $_POST['alone'] == 'true'; 

      $dealer = '';
      $cardFaceUp = '';
      
      $conn = mysqli_connect($hostname, $username, $password, $dbname);

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
      
      if (strlen($cardFaceUp) == 3 && $cardFaceUp[2] == 'D') {
        $cardFaceUp = getCardFaceUp($cardFaceUp, $positionID, $alone);
        $turn = getTurn($cardFaceUp, $alone, $dealer);

        $sqlTail = ",`CardFaceUp` = '{$cardFaceUp}',`Turn` = '{$turn}',`Lead` = null where `ID`='{$gameID}'";
        
        if ($positionID == 'O' || $positionID == 'P') {
          $sql = "update `Game` set `OrganizerTrump` = '{$trumpID}'".$sqlTail;
        } else {
          $sql = "update `Game` set `OpponentTrump` = '{$trumpID}'".$sqlTail;
        }
        
        if (mysqli_query($conn, $sql) === false) {
          $response['ErrorMsg'] .= mysqli_error($conn);
        }
      } else {
        $response['ErrorMsg'] .= "chooseTrump: Invalid game state. ";
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

  function getCardFaceUp($cardFaceUp, $positionID, $alone){
    $c = $cardFaceUp.$positionID;
    if ($alone) {
      $c .= getPlayerSkipped($positionID);
    }
    return $c;
  }
  
  function getTurn($cardFaceUp, $alone, $dealer){
    $t = getNextTurn($dealer);
    if ($alone) {
      $playerSkipped = $cardFaceUp[4];
      if ($playerSkipped == $t) {
        $t = getNextTurn($t);
      }
    }
    return $t;
  }
  
?>