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

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'One or more request parameters are missing or invalid']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $playerID = $_POST['r'];
  
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
    // get Dealer and CardFaceUp
    $dealer = '';
    $cardFaceUp = '';
    $sql = "select `Dealer`, `CardFaceUp` from `Game` where `ID` = ?";
    $stmt = mysqli_prepare($connection, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($connection)); }

    mysqli_stmt_bind_param($stmt, 's', $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $dealer = $row['Dealer'] ?? '';
        $cardFaceUp = $row['CardFaceUp'] ?? '';
    }
    mysqli_stmt_close($stmt);

    setTurnSetTrump($conn, $cardFaceUp, $gameID, $dealer);
    
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
  
  function setTurnSetTrump($conn, $cardFaceUp, $gameID, $dealer) {
    $cardID = substr($cardFaceUp,0,2);
    $trump = substr($cardFaceUp,1,1);
    $playerID = substr($cardFaceUp,3,1);
    $trumpColumn = $playerID == 'O' || $playerID == 'P' ? 'OrganizerTrump' : 'OpponentTrump';
    $turn = getNextTurn($dealer);
    $cardFaceUpModified = $cardID.'K'.substr($cardFaceUp,3);
    
    $sql = "update `Game` set `{$trumpColumn}` = ?, `CardFaceUp` = ?, `Turn` = ? where `ID` = ?";
    $stmt = mysqli_prepare($connection, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($connection)); }

    mysqli_stmt_bind_param($stmt, 'ssss', $trump, $cardFaceUpModified, $turn, $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
    
    mysqli_stmt_close($stmt);
  }
?>