<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID
  include('../svc/getNextTurn.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $opponentTricks = $_POST['opponentTricks'];
      $opponentScore = $_POST['opponentScore'];
      $organizerTricks = $_POST['organizerTricks'];
      $organizerScore = $_POST['organizerScore'];
      $winner = $_POST['winner'];
      
      $sql = "update `Game` set `OrganizerTricks` = {$organizerTricks},`OrganizerScore` = {$organizerScore},`OpponentTricks` = {$opponentTricks},`OpponentScore` = {$opponentScore},`ACO` = null,`ACP` = null,`ACL` = null,`ACR` = null,`PO` = null,`PP` = null,`PL` = null,`PR` = null where `ID`='{$gameID}'";
      $result = mysqli_query($connection, $sql);
      if ($result === false) {
        $response['ErrorMsg'] .= mysqli_error($connection);
      }
    
      if ($opponentTricks == 0 && $organizerTricks == 0) {
        $dealer = "";
        $sql = "select `Dealer` from `Game` where `ID`='{$gameID}'";

        $results = mysqli_query($connection, $sql);
        if ($results === false) {
          $response['ErrorMsg'] .= mysqli_error($connection);
        } else {
          while ($row = mysqli_fetch_array($results)) {
            $dealer = is_null($row['Dealer']) ? '' : $row['Dealer'];
          }
        }
        
        if (strlen($dealer) == 1) {
          $dealer = getNextTurn($dealer);
          $turn = getNextTurn($dealer);
          $sql = "update `Game` set `Dealer` = '{$dealer}',`Lead` = null,`Turn` = '{$turn}',`OrganizerTrump` = null,`OpponentTrump` = null where `ID`='{$gameID}'";
          $result = mysqli_query($connection, $sql);
          if ($result === false) {
            $response['ErrorMsg'] .= mysqli_error($connection);
          }
        } else {
            $response['ErrorMsg'] .= "Invalid dealer: '{$dealer}'";
        }
      } else {
        $sql = "update `Game` set `Lead` = null,`Turn` = '{$winner}' where `ID`='{$gameID}'";
        $result = mysqli_query($connection, $sql);
        if ($result === false) {
          $response['ErrorMsg'] .= mysqli_error($connection);
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