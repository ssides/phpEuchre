<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $dealID = strlen($_POST['dealID']) > 0 ? $_POST['dealID'] : null;
      $positionID = $_POST['positionID'];
      $state = $_POST['state'];
      $message = strlen($_POST['message']) > 0 ? $_POST['message'] : null;
      $organizerScore = $_POST['organizerScore'];
      $opponentScore = $_POST['opponentScore'];
      $organizerTricks = $_POST['organizerTricks'];
      $opponentTricks = $_POST['opponentTricks'];
      $id = GUID();

      $sql = "insert into `GameControllerLog` (`ID`,`GameID`,`DealID`,`PositionID`,`GameControllerState`,`Message`,`OrganizerScore`,`OpponentScore`,`OrganizerTricks`,`OpponentTricks`,`InsertDate`) values (?,?,?,?,?,?,?,?,?,?,now())";
      
      $smt = mysqli_prepare($connection, $sql);
      mysqli_stmt_bind_param($smt, 'ssssssiiii', $id,$gameID,$dealID,$positionID,$state,$message,$organizerScore,$opponentScore,$organizerTricks,$opponentTricks);
      if (!mysqli_stmt_execute($smt)){
        $response['ErrorMsg'] .= mysqli_error($connection);
      }
      
      mysqli_stmt_close($smt);

      http_response_code(200);
      
      echo json_encode($response);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>