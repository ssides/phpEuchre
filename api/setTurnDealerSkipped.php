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
      
      $dealer = '';
      $cardFaceUp = '';
      
      $sql = "select `Dealer`,`CardFaceUp` from `Game` where `ID`='{$gameID}'";

      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($connection);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $dealer = is_null($row['Dealer']) ? '' : $row['Dealer'];
          $cardFaceUp = is_null($row['CardFaceUp']) ? '' : $row['CardFaceUp'];
        }
        
        $response['ErrorMsg'] .= setTurnSetTrump($cardFaceUp, $gameID, $dealer);
      }
      
      http_response_code(200);
      
      echo json_encode($response);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

  function setTurnSetTrump($cardFaceUp, $gameID, $dealer) {
    global $connection;
    
    $response = "";
    $cardID = substr($cardFaceUp,0,2);
    $trump = substr($cardFaceUp,1,1);
    $playerID = substr($cardFaceUp,3,1);
    $trumpColumn = $playerID == 'O' || $playerID == 'P' ? 'OrganizerTrump' : 'OpponentTrump';
    $turn = getNextTurn($dealer);
    
    $cardFaceUp = $cardID.'K'.substr($cardFaceUp,3);
    $sql = "update `Game` set `{$trumpColumn}` = '{$trump}',`CardFaceUp` = '{$cardFaceUp}',`Turn` = '{$turn}' where `ID`='{$gameID}'";
    mysqli_query($connection, "START TRANSACTION;");
    $results = mysqli_query($connection, $sql);
    if ($results === false) {
      $response .= mysqli_error($connection);
      mysqli_query($connection, "ROLLBACK;");
    } else {
      mysqli_query($connection, "COMMIT;");
    }
    
    return $response;
  }
?>