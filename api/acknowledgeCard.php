<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  // I as positionID am acknowledging that I have seen the card played by playerID.
  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID']) || !isset($_POST['positionID']) || strpos("OPLR", $_POST['positionID']) === false) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication, gameID, or positionID']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $positionID = $_POST['positionID'];
  $playerID = $_POST['playerID'];
  
  $ack = "";
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
    $sql = "SELECT `AC{$positionID}` AS `ACK` FROM `Game` WHERE `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
      throw new Exception(mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $gameID);
    if (!mysqli_stmt_execute($stmt)) {
      throw new Exception(mysqli_error($conn));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_array($result)) {
      $ack = is_null($row['ACK']) ? '' : $row['ACK'];
    }
    mysqli_stmt_close($stmt);

    if (strpos($ack, $playerID) === false) {
      $ack .= $playerID;
      // Prepare statement to update ACK field
      $sql = "UPDATE `Game` SET `AC{$positionID}` = ? WHERE `ID` = ?";
      $stmt = mysqli_prepare($conn, $sql);
      if ($stmt === false) { throw new Exception(mysqli_error($conn)); }

      mysqli_stmt_bind_param($stmt, "ss", $ack, $gameID);
      if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_error($conn));
      }
      mysqli_stmt_close($stmt);
    } else {
      $response['ErrorMsg'] = "{$positionID} already acknowledged card played by {$playerID}.";
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