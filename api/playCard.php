<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getHand.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $positionID = $_POST['positionID'];
      $cardID = $_POST['cardID'];
      
      $hand = getHand($gameID, $positionID);
      $cardNumber = getCardNumber($hand, $cardID);
      $cards = "";
      
      $sql = "select `PO`,`PP`,`PL`,`PR` from `Game` where `ID`='{$gameID}'";
      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($connection);
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
        $results = mysqli_query($connection, $sql);
        if ($results === false) {
          $response['ErrorMsg'] .= mysqli_error($connection);
        }
        
        $sql = "update `Game` set `P{$positionID}` = '{$cardID}' where `ID`='{$gameID}'";
        $results = mysqli_query($connection, $sql);
        if ($results === false) {
          $response['ErrorMsg'] .= mysqli_error($connection);
        }
        
        if (strlen($cards) == 0) {
          $sql = "update `Game` set `Lead` = '{$positionID}' where `ID`='{$gameID}'";
          $results = mysqli_query($connection, $sql);
          if ($results === false) {
            $response['ErrorMsg'] .= mysqli_error($connection);
          }
        }
      } else {
        $response['ErrorMsg'] .= "Play state error.";
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