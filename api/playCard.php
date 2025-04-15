<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getHand.php');
  
  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID']) || !isset($_POST['cardID']) || !isset($_POST['positionID']) || strlen($_POST['positionID']) != 1 || strpos("OPLR", $_POST['positionID']) === false) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'One or more request parameters are missing or invalid']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $positionID = $_POST['positionID'];
  $cardID = $_POST['cardID'];

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if (!$conn) {
    trigger_error("Database connection failed: " . mysqli_connect_error(), E_USER_ERROR);
    http_response_code(500);
    $response['ErrorMsg'] = "Internal server error.";
    echo json_encode($response);
    exit;
  }

  mysqli_begin_transaction($conn);

  try {
    $hand = getHand($conn, $gameID, $positionID);

    $cardNumber = getCardNumber($hand, $cardID);
    $cards = "";
    
    $sql = "select `PO`,`PP`,`PL`,`PR` from `Game` where `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, "s", $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_error($conn)); }

    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) { throw new Exception(mysqli_stmt_error($stmt)); }

    while ($row = mysqli_fetch_array($results)) {
      $cards .= is_null($row['PO']) ? '' : $row['PO'];
      $cards .= is_null($row['PP']) ? '' : $row['PP'];
      $cards .= is_null($row['PL']) ? '' : $row['PL'];
      $cards .= is_null($row['PR']) ? '' : $row['PR'];
    }
    
    mysqli_stmt_close($stmt);

    if (strlen($hand['PlayID']) == 0 || $cardNumber == '0' || strlen($positionID) != 1) {
      throw new Exception("Play state error.");
    }
    
    showPlayerCardPlayed($conn, $cardNumber, $p, $hand['PlayID']);
    showGameCardPlayed($conn, $positionID, $cardID, $gameID);
    
    if (strlen($cards) == 0) { // if this is the first card of the trick 
      showLeader($conn, $positionID, $gameID);
    }
    
    mysqli_commit($conn);
    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    mysqli_rollback($conn);
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo json_encode(['ErrorMsg' => 'An error occurred while updating the game.']);
  }

  mysqli_close($conn);

  // functions
  
  function showPlayerCardPlayed($conn, $cardNumber, $p, $h) {
    $sql = "update `Play` set `CardID{$cardNumber}` = ? where `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_bind_param($stmt, "ss", $p, $h);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_close($stmt);
  }
  
  function showGameCardPlayed($conn, $positionID, $cardID, $gameID) {
    $sql = "update `Game` set `P{$positionID}` = ? where `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_bind_param($stmt, "ss", $cardID, $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_close($stmt);
  }
  
  function showLeader($conn, $positionID, $gameID) {
    $sql = "update `Game` set `Lead` = ? where `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_bind_param($stmt, "ss", $positionID, $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_close($stmt);
  }

?>