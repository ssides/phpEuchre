<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getNextTurn.php');

  trigger_error(basename(__FILE__)); // debug
  

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID']) || !isset($_POST['positionID']) || strlen($_POST['positionID']) != 1 || strpos("OPLR", $_POST['positionID']) === false) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'One or more request parameters are missing or invalid']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $playerID = $_POST['r'];
  $positionID = $_POST['positionID'];

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
    $turn = getNextTurn($positionID);

    $sql = "update `Game` set `Turn` = ? where `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, 'ss', $turn, $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception('Execute failed: ' . mysqli_stmt_error($stmt)); }

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