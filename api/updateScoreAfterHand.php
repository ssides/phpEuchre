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
      $opponentTricks = $_POST['opponentTricks'];
      $opponentScore = $_POST['opponentScore'];
      $organizerTricks = $_POST['organizerTricks'];
      $organizerScore = $_POST['organizerScore'];
      $winner = $_POST['winner'];
      
      $conn = mysqli_connect($hostname, $username, $password, $dbname);
    
      mysqli_query($conn, "START TRANSACTION;");

      $sql = "update `Game` set 
        `OrganizerTricks` = {$organizerTricks}
        ,`OrganizerScore` = {$organizerScore}
        ,`OpponentTricks` = {$opponentTricks}
        ,`OpponentScore` = {$opponentScore}
        ,`ACO` = null
        ,`ACP` = null
        ,`ACL` = null
        ,`ACR` = null
        ,`PO` = null
        ,`PP` = null
        ,`PL` = null
        ,`PR` = null
        ,`ScoringInProgress` = '1'
        where `ID`= '{$gameID}'";
      
      $results = mysqli_query($conn, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
      }

      if ($opponentTricks == 0 && $organizerTricks == 0) {
        $response['ErrorMsg'] .= setDealInActive($conn, $gameID);
        $d = getDealer($conn, $gameID);
        $response['ErrorMsg'] .= $d['ErrorMsg'];
        $dealer = $d['Dealer'];
        
        if (strlen($dealer) == 1) {
          $dealer = getNextTurn($dealer);
          $turn = getNextTurn($dealer);
          $sql = "update `Game` set `Dealer` = '{$dealer}',`Lead` = null,`Turn` = '{$turn}',`OrganizerTrump` = null,`OpponentTrump` = null where `ID`='{$gameID}'";
              
          $result = mysqli_query($conn, $sql);
          if ($result === false) {
            $response['ErrorMsg'] .= mysqli_error($conn);
          }
        } else {
            $response['ErrorMsg'] .= "Invalid dealer: '{$dealer}'";
        }
      } else {
        $sql = "update `Game` set `Lead` = null,`Turn` = '{$winner}' where `ID`='{$gameID}'";
        if (mysqli_query($conn, $sql) === false) {
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
  
  function getDealer($conn, $gameID) {
    $response = array();
    $response['ErrorMsg'] = "";
    $response['Dealer'] = "";

    $sql = "select `Dealer` from `Game` where `ID`='{$gameID}'";

    $results = mysqli_query($conn, $sql);
    if ($results === false) {
      $response['ErrorMsg'] .= mysqli_error($conn);
    } else {
      while ($row = mysqli_fetch_array($results)) {
        $response['Dealer'] = is_null($row['Dealer']) ? '' : $row['Dealer'];
      }
    }
        
    return $response;
  }
  
  function setDealInActive($conn, $gameID){
    $errorMsg = "";
    
    $sql = "update `GameDeal` set `IsActive` = '0' where `GameID` = '{$gameID}' and `IsActive` = '1'";
    if (mysqli_query($conn, $sql) === false) {
      $errorMsg = mysqli_error($conn);
    } 

    return $errorMsg;
  }
?>