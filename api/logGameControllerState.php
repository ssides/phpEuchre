<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID
  
  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID']) || !isset($_POST['positionID']) || !isset($_POST['state']) ) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication, gameID, or positionID']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $id = GUID();
  $playerID = $_POST['r'];
  $gameID = $_POST['gameID'];
  $dealID = !empty($_POST['dealID']) ? $_POST['dealID'] : null;
  $positionID = $_POST['positionID'];
  $state = $_POST['state'];
  $message = !empty($_POST['message']) ? trim($_POST['message']) : null;
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


  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if (!$conn) {
    trigger_error("Database connection failed: " . mysqli_connect_error(), E_USER_ERROR);
    http_response_code(500);
    echo json_encode(['ErrorMsg' => 'Internal server error.']);
    exit;
  }
  
  try {
    $stmt = mysqli_prepare($conn, "insert into `GameControllerLog` 
        (`ID`, `GameID`, `DealID`, `PlayerID`, `PositionID`, `GameControllerState`, `Message`, 
         `OrganizerScore`, `OpponentScore`, `OrganizerTricks`, `OpponentTricks`, 
         `Dealer`, `Turn`, `CardFaceUp`, `ACO`, `ACP`, `ACL`, `ACR`, 
         `PO`, `PP`, `PL`, `PR`, `InsertDate`) 
        values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, curtime(3))");
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, "sssssssiiiisssssssssss", 
        $id,        $gameID,        $dealID,        $playerID,        $positionID,        $state,        $message,
        $organizerScore,        $opponentScore,        $organizerTricks,        $opponentTricks,
        $dealer,        $turn,        $cardFaceUp,        $aco,        $acp,        $acl,        $acr,
        $po,        $pp,        $pl,        $pr
    );
    
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_close($stmt);

    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo json_encode(['ErrorMsg' => 'An error occurred while updating the game.']);
  }

  mysqli_close($conn);

?>