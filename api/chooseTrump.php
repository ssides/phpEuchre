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

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID']) || !isset($_POST['positionID']) || strpos("OPLR", $_POST['positionID']) === false || !isset($_POST['trumpID'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication, gameID, positionID, or trumpID']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $positionID = $_POST['positionID'];
  $trumpID = $_POST['trumpID'];
  $alone = $_POST['alone'] == 'true'; 

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
    $stmt = mysqli_prepare($conn, "SELECT `Dealer`, `CardFaceUp` FROM `Game` WHERE `ID` = ?");
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, "s", $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_error($conn)); }

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $dealer = $row['Dealer'] ?? '';
        $cardFaceUp = $row['CardFaceUp'] ?? '';
    } else {
        throw new Exception("Game not found.");
    }
    mysqli_stmt_close($stmt);

    if (strlen($cardFaceUp) == 3 && $cardFaceUp[2] == 'D') {
      $cardFaceUp = getCardFaceUp($cardFaceUp, $positionID, $alone);
      $turn = getTurn($cardFaceUp, $alone, $dealer);

      // Prepare update query
      if ($positionID == 'O' || $positionID == 'P') {
        $sql = "UPDATE `Game` SET `OrganizerTrump` = ?, `CardFaceUp` = ?, `Turn` = ?, `Lead` = NULL WHERE `ID` = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) { throw new Exception(mysqli_error($conn)); }

        mysqli_stmt_bind_param($stmt, "ssss", $trumpID, $cardFaceUp, $turn, $gameID);
      } else {
        $sql = "UPDATE `Game` SET `OpponentTrump` = ?, `CardFaceUp` = ?, `Turn` = ?, `Lead` = NULL WHERE `ID` = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) { throw new Exception(mysqli_error($conn)); }
        
        mysqli_stmt_bind_param($stmt, "ssss", $trumpID, $cardFaceUp, $turn, $gameID);
      }

      if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_error($conn));
      }
      mysqli_stmt_close($stmt);
    } else {
      throw new Exception("Invalid game state.");
    }

    mysqli_commit($conn);
    http_response_code(200);
    echo json_encode($response);
    
  } catch(Exception $e) {
    mysqli_rollback($conn);
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    $response['ErrorMsg'] = 'An error occurred while updating the game.';
    echo json_encode($response);
  }
  
  mysqli_close($conn);


  function getCardFaceUp($cardFaceUp, $positionID, $alone) {
    $c = $cardFaceUp . $positionID;
    if ($alone) {
      $c .= getPlayerSkipped($positionID);
    }
    return $c;
  }

  function getTurn($cardFaceUp, $alone, $dealer) {
    $t = getNextTurn($dealer);
    if ($alone) {
      $playerSkipped = $cardFaceUp[4];
      if ($playerSkipped == $t) {
        $t = getNextTurn($t);
      }
    }
    return $t;
  }

?>