<?php 

// As "position" I am acknowledging that I've seen the Jack or that Scoring is in progress.

include_once('../config/db.php');
include_once('../config/config.php');
include('../controllers/isAuthenticated.php');

if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
  http_response_code(405); // Method Not Allowed
  echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
  exit;
}

if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID']) || !isset($_POST['position']) || strpos("OPLR", $_POST['position']) === false) {
  http_response_code(400); // Bad Request
  echo json_encode(['ErrorMsg' => 'Missing or invalid authentication, gameID, or position']);
  exit;
}

$response = ['ErrorMsg' => ''];
$gameID = $_POST['gameID'];
$position = $_POST['position'];

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
  $sql = "UPDATE `Game` SET `AC{$position}` = ? WHERE `ID` = ?";
  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    throw new Exception(mysqli_error($conn));
  }

  $value = 'A';
  mysqli_stmt_bind_param($stmt, 'ss', $value, $gameID);
  if (!mysqli_stmt_execute($stmt)) {
    throw new Exception(mysqli_error($conn));
  }

  mysqli_commit($conn);
  http_response_code(200);
  echo json_encode($response);

} catch (Exception $e) {
  mysqli_rollback($conn);
  // Log the exception to /var/log/php_errors.log
  // error_log("[" . date('Y-m-d H:i:s') . "] Database error: " . $e->getMessage() . "\n", 3, '/var/log/php_errors.log');
  // better to use trigger_error() as follows.
  trigger_error($e->getMessage(), E_USER_ERROR);

  http_response_code(500); // Internal Server Error
  $response['ErrorMsg'] = 'An error occurred while updating the game';
  echo json_encode($response);
}

mysqli_close($conn);
?>