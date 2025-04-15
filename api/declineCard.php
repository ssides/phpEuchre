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

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID']) || !isset($_POST['positionID']) || strpos("OPLR", $_POST['positionID']) === false) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication, gameID, or positionID']);
    exit;
  }

  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $playerID = $_POST['r'];
  $positionID = $_POST['positionID'];
  $dealer = '';
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
    
    // Prepare statement for selecting game data
    $sql = "SELECT `Dealer`, `CardFaceUp` FROM `Game` WHERE `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, "s", $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) { throw new Exception("Invalid GameID."); }
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    $dealer = $row['Dealer'] ?? '';
    $cardFaceUp = $row['CardFaceUp'] ?? '';

    if (strlen($dealer) > 0 && strlen($cardFaceUp) > 0) {
      if ($dealer != $positionID) {
        throw new Exception("Invalid PositionID: Dealer: {$dealer} PositionID: {$positionID}");
      } else {
          $cardFaceUp .= 'D';
          $turn = getNextTurn($positionID);

          // Prepare statement for updating game
          $sql = "UPDATE `Game` SET `CardFaceUp` = ?, `Turn` = ? WHERE `ID` = ?";
          $stmt = mysqli_prepare($conn, $sql);
          if (!$stmt) { throw new Exception( mysqli_error($conn)); }

          mysqli_stmt_bind_param($stmt, "sss", $cardFaceUp, $turn, $gameID);
          if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
          
          mysqli_stmt_close($stmt);
      }
    } else {
        $response['ErrorMsg'] = "declineCard: Invalid game state";
    }

    mysqli_commit($conn);
    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    mysqli_rollback($conn);
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    $response['ErrorMsg'] = 'An error occurred while declining the card.';
    echo json_encode($response);
  }

  mysqli_close($conn);

?>