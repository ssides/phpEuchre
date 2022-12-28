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
      
      $conn1 = mysqli_connect($hostname, $username, $password, $dbname);
    
      $sql = "update `Game` set 
        `OrganizerTricks` = ?
        ,`OrganizerScore` = ?
        ,`OpponentTricks` = ?
        ,`OpponentScore` = ?
        ,`ACO` = null
        ,`ACP` = null
        ,`ACL` = null
        ,`ACR` = null
        ,`PO` = null
        ,`PP` = null
        ,`PL` = null
        ,`PR` = null
        ,`ScoringInProgress` = '1'
        where `ID`= ?";
      
      $smt = mysqli_prepare($conn1, $sql);
      mysqli_stmt_bind_param($smt, 'iiiis', $organizerTricks,$organizerScore,$opponentTricks,$opponentScore,$gameID);
      if (!mysqli_stmt_execute($smt)){
        $response['ErrorMsg'] .= mysqli_error($conn1);
      }

      mysqli_stmt_close($smt);

      mysqli_close($conn1);
      
      $conn2 = mysqli_connect($hostname, $username, $password, $dbname);

      if ($opponentTricks == 0 && $organizerTricks == 0) {
        $response['ErrorMsg'] .= setDealInActive($gameID);
        $dealer = "";
        $sql = "select `Dealer` from `Game` where `ID`='{$gameID}'";

        $results = mysqli_query($conn2, $sql);
        if ($results === false) {
          $response['ErrorMsg'] .= mysqli_error($conn2);
        } else {
          while ($row = mysqli_fetch_array($results)) {
            $dealer = is_null($row['Dealer']) ? '' : $row['Dealer'];
          }
        }
        
        if (strlen($dealer) == 1) {
          $dealer = getNextTurn($dealer);
          $turn = getNextTurn($dealer);
          $sql = "update `Game` set `Dealer` = '{$dealer}',`Lead` = null,`Turn` = '{$turn}',`OrganizerTrump` = null,`OpponentTrump` = null where `ID`='{$gameID}'";
          $result = mysqli_query($conn2, $sql);
          if ($result === false) {
            $response['ErrorMsg'] .= mysqli_error($conn2);
          }
        } else {
            $response['ErrorMsg'] .= "Invalid dealer: '{$dealer}'";
        }
      } else {
        $sql = "update `Game` set `Lead` = null,`Turn` = '{$winner}' where `ID`='{$gameID}'";
        $result = mysqli_query($conn2, $sql);
        if ($result === false) {
          $response['ErrorMsg'] .= mysqli_error($conn2);
        }
      }
      
      mysqli_close($conn2);

      http_response_code(200);
      
      echo json_encode($response);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
  
  function setDealInActive($gameID){
    global $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $errorMsg = "";
    
    $sql = "update `GameDeal` set `IsActive` = '0' where `GameID` = '{$gameID}' and `IsActive` = '1'";
    if (mysqli_query($conn, $sql) === false) {
      $errorMsg = mysqli_error($conn);
    } 

    return $errorMsg;
  }
?>