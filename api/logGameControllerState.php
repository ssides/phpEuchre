<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $id = GUID();
      $gameID = $_POST['gameID'];
      $dealID = strlen($_POST['dealID']) > 0 ? $_POST['dealID'] : null;
      $positionID = $_POST['positionID'];
      $state = $_POST['state'];
      $message = strlen($_POST['message']) > 0 ? $_POST['message'] : null;
      $organizerScore = $_POST['organizerScore'];
      $opponentScore = $_POST['opponentScore'];
      $organizerTricks = $_POST['organizerTricks'];
      $opponentTricks = $_POST['opponentTricks'];
      $dealer = $_POST['dealer'];
      $turn = $_POST['turn'];
      $cardFaceUp = $_POST['cardFaceUp'];
      $aco = $_POST['aco'];
      $acp = $_POST['acp'];
      $acl = $_POST['acl'];
      $acr = $_POST['acr'];
      $po = $_POST['po'];
      $pp = $_POST['pp'];
      $pl = $_POST['pl'];
      $pr = $_POST['pr'];

      $sql = "insert into `GameControllerLog` 
        (`ID`,`GameID`,`DealID`,`PositionID`,`GameControllerState`,`Message`,`OrganizerScore`,`OpponentScore`,`OrganizerTricks`,`OpponentTricks`,`Dealer`,`Turn`,`CardFaceUp`,`ACO`,`ACP`,`ACL`,`ACR`,`PO`,`PP`,`PL`,`PR`,`InsertDate`) 
        values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,curtime(3))";
      
      $smt = mysqli_prepare($connection, $sql);
      mysqli_stmt_bind_param($smt, 'ssssssiiiisssssssssss', $id,$gameID,$dealID,$positionID,$state,$message,$organizerScore,$opponentScore,$organizerTricks,$opponentTricks,$dealer,$turn,$cardFaceUp,$aco,$acp,$acl,$acr,$po,$pp,$pl,$pr);
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