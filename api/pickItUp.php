<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getNextTurn.php');

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID']) || !isset($_POST['alone']) || !isset($_POST['positionID']) || strpos("OPLR", $_POST['positionID']) === false) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'One or more request parameters are missing or invalid']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $playerID = $_POST['r'];
  $positionID = $_POST['positionID']; // who ordered it up.
  $alone = $_POST['alone'] == 'true'; 
  $cardFaceUp = '';

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
    
    // Select CardFaceUp
    $sql = "select `CardFaceUp` from `Game` where `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_bind_param($stmt, 's', $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_array($result)) {
      $cardFaceUp = $row['CardFaceUp'] ?? '';
    }
    mysqli_stmt_close($stmt);

    if (strlen($cardFaceUp) != 2) { throw new Exception("Invalid game state. CardFaceUp: {$cardFaceUp}"); }
    
    $cardFaceUp .= "U{$positionID}";
    if ($alone) {
      $cardFaceUp .= getPlayerSkipped($positionID);
    }

    // Update CardFaceUp
    $sql = "update `Game` set `CardFaceUp` = ? where `ID` = ?";
    if (!mysqli_begin_transaction($conn)) { throw new Exception(mysqli_error($conn)); }

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_bind_param($stmt, 'ss', $cardFaceUp, $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
    
    mysqli_stmt_close($stmt);
    
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

?>