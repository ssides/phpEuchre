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
      $positionID = $_POST['positionID'];
      $trumpID = $_POST['trumpID'];
      $alone = $_POST['alone'] == 'true'; 

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
      
      if (strlen($cardFaceUp) == 3 && $cardFaceUp[2] == 'D') {
        
        $cardFaceUp = getCardFaceUp($cardFaceUp, $positionID, $alone);
        $turn = getTurn($cardFaceUp, $alone, $dealer);

        $sqlTail = ",`CardFaceUp` = '{$cardFaceUp}',`Turn` = '{$turn}',`Lead` = null where `ID`='{$gameID}'";
        
        if ($positionID == 'O' || $positionID == 'P') {
          $sql = "update `Game` set `OrganizerTrump` = '{$trumpID}'".$sqlTail;
        } else {
          $sql = "update `Game` set `OpponentTrump` = '{$trumpID}'".$sqlTail;
        }
        
        mysqli_query($connection, "START TRANSACTION;");
        if (mysqli_query($connection, $sql) === false) {
          $response['ErrorMsg'] .= mysqli_error($connection);
          mysqli_query($connection, "ROLLBACK;");
        } else {
          mysqli_query($connection, "COMMIT;");
        }

      } else {
        $response['ErrorMsg'] .= "Wrong state error. ";
      }
      
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