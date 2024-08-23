<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $id = GUID();
      $playerID = $_POST['r'];
      $gameID = $_POST['gameID'];
      $dealID = strlen($_POST['dealID']) > 0 ? "'".$_POST['dealID']."'" : 'null';
      $positionID = $_POST['positionID'];
      $state = $_POST['state'];
      $message = strlen($_POST['message']) > 0 ? "'".$_POST['message']."'" : 'null';
      $organizerScore = strlen($_POST['organizerScore']) > 0 ? $_POST['organizerScore'] : '0';
      $opponentScore = strlen($_POST['opponentScore']) > 0 ? $_POST['opponentScore'] : '0';
      $organizerTricks = strlen($_POST['organizerTricks']) > 0 ? $_POST['organizerTricks'] : '0';
      $opponentTricks = strlen($_POST['opponentTricks']) > 0 ? $_POST['opponentTricks'] : '0';
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
        (`ID`,`GameID`,`DealID`,`PlayerID`,`PositionID`,`GameControllerState`,`Message`,`OrganizerScore`,`OpponentScore`,`OrganizerTricks`,`OpponentTricks`,`Dealer`,`Turn`,`CardFaceUp`,`ACO`,`ACP`,`ACL`,`ACR`,`PO`,`PP`,`PL`,`PR`,`InsertDate`) 
        values ('{$id}','{$gameID}',{$dealID},'{$playerID}','{$positionID}','{$state}',{$message},{$organizerScore},{$opponentScore},{$organizerTricks},{$opponentTricks},'{$dealer}','{$turn}','{$cardFaceUp}','{$aco}','{$acp}','{$acl}','{$acr}','{$po}','{$pp}','{$pl}','{$pr}',curtime(3))";
      
      $conn = mysqli_connect($hostname, $username, $password, $dbname);

      if (mysqli_query($conn, $sql) === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
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

?>