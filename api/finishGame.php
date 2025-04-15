<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication or gameID']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];

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
    $sql = "UPDATE `Game` SET `GameFinishDate` = NOW(), `Dealer` = NULL, `Turn` = NULL, `CardFaceUp` = NULL WHERE `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }

    if (!mysqli_stmt_bind_param($stmt, "s", $gameID)) {
      throw new Exception(mysqli_stmt_error($stmt));
    }

    if (!mysqli_stmt_execute($stmt)) {
      throw new Exception(mysqli_stmt_error($stmt));
    }
    
    mysqli_commit($conn);
    http_response_code(200);
    echo json_encode($response);
    
  } catch (Exception $e) {
    mysqli_rollback($conn);
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    $response['ErrorMsg'] = 'An error occurred while updating the game.';
    echo json_encode($response);
  }

  mysqli_close($conn);

?>