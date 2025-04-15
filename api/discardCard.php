<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getHand.php');
  include('../svc/getNextTurn.php');
  include('../svc/goingAlone.php');

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID']) || !isset($_POST['cardID']) || !isset($_POST['positionID']) || strpos("OPLR", $_POST['positionID']) === false) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication, gameID, cardID, or positionID']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $playerID = $_POST['r'];
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
    
    $sql = "SELECT `Dealer`, `CardFaceUp` FROM `Game` WHERE `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_bind_param($stmt, "s", $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_error($conn)); }

    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) { throw new Exception("Invalid GameID."); }
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    $dealer = $row['Dealer'] ?? '';
    $cardFaceUp = $row['CardFaceUp'] ?? '';

    if (strlen($dealer) > 0 && $dealer == $positionID && strlen($cardFaceUp) > 3 && $cardFaceUp[2] == 'U') {
      $hand = getHand($conn, $gameID, $positionID);

      $cardNumber = getCardNumber($hand, $cardID);

      if ($cardNumber == '0') {
        throw new Exception("Card not found '{$cardID}'. ");
      } else {
        saveHandSetTurn($conn, $hand, $cardNumber, $cardFaceUp, $gameID, $dealer);
      }
    } else {
      throw new Exception("Invalid game state.");
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

  // functions
  
  function saveHandSetTurn($conn, $hand, $cardNumber, $cardFaceUp, $gameID, $dealer) {
    $cardID = substr($cardFaceUp, 0, 2);
    $trump = substr($cardFaceUp, 1, 1);
    $playerID = substr($cardFaceUp, 3, 1);
    $trumpColumn = $playerID == 'O' || $playerID == 'P' ? 'OrganizerTrump' : 'OpponentTrump';
    $turn = getNextTurn($dealer);

    if (getAlone($cardFaceUp)) {
      $skipped = getSkippedPosition($cardFaceUp[3]);
      if ($turn == $skipped) {
        $turn = getNextTurn($turn);
      }
    }

    // Update Play table
    $sql = "UPDATE `Play` SET `CardID{$cardNumber}` = ? WHERE `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, "ss", $cardID, $hand['PlayID']);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_error($conn)); }
    mysqli_stmt_close($stmt);

    // Update Game table
    $cardFaceUp = $cardID . 'S' . substr($cardFaceUp, 3);
    $sql = "UPDATE `Game` SET `{$trumpColumn}` = ?, `CardFaceUp` = ?, `Turn` = ? WHERE `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_bind_param($stmt, "ssss", $trump, $cardFaceUp, $turn, $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_close($stmt);
  }
  
?>