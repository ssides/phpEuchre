<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getHand.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $positionID = $_POST['positionID'];
      $cardID = $_POST['cardID'];
      
      $conn = mysqli_connect($hostname, $username, $password, $dbname);

      mysqli_query($conn, "START TRANSACTION;");

      $hand = getHand($conn, $gameID, $positionID);
      if (strlen($hand['ErrorMsg']) > 0) {
        $response['ErrorMsg'] .= $hand['ErrorMsg'];
      }
      $cardNumber = getCardNumber($hand, $cardID);
      $cards = "";
      
      $sql = "select `PO`,`PP`,`PL`,`PR` from `Game` where `ID`='{$gameID}'";
      $results = mysqli_query($conn, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $cards .= is_null($row['PO']) ? '' : $row['PO'];
          $cards .= is_null($row['PP']) ? '' : $row['PP'];
          $cards .= is_null($row['PL']) ? '' : $row['PL'];
          $cards .= is_null($row['PR']) ? '' : $row['PR'];
        }
      }

      if (strlen($hand['PlayID']) > 0 && $cardNumber != '0' && strlen($positionID) == 1) {
        $p = $cardID.'P';
        $sql = "update `Play` set `CardID{$cardNumber}` = '{$p}' where `ID`='{$hand['PlayID']}'";
        run($conn, $response, $sql);
        
        $sql = "update `Game` set `P{$positionID}` = '{$cardID}' where `ID`='{$gameID}'";
        run($conn, $response, $sql);
        
        if (strlen($cards) == 0) {
          $sql = "update `Game` set `Lead` = '{$positionID}' where `ID`='{$gameID}'";
          run($conn, $response, $sql);
        }
      } else {
        $response['ErrorMsg'] .= "Play state error.";
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

  function run($conn, $response, $sql) {
    $results = mysqli_query($conn, $sql);
    if ($results === false) {
      $response['ErrorMsg'] .= mysqli_error($conn);
    }
  }
?>