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

  if (!isset($_POST['r']) 
    || !isAuthenticated($_POST['r']) 
    || !isset($_POST['gameID']) 
    || !isset($_POST['opponentTricks']) 
    || !isset($_POST['opponentScore']) 
    || !isset($_POST['organizerTricks']) 
    || !isset($_POST['organizerScore']) 
    || !isset($_POST['winner']) 
    ) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'One or more request parameters are missing or invalid']);
    exit;
  }

  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $opponentTricks = $_POST['opponentTricks'];
  $opponentScore = $_POST['opponentScore'];
  $organizerTricks = $_POST['organizerTricks'];
  $organizerScore = $_POST['organizerScore'];
  $winner = $_POST['winner'];

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
    // Update game
    $sql = "update `Game` set 
        `OrganizerTricks` = ?,
        `OrganizerScore` = ?,
        `OpponentTricks` = ?,
        `OpponentScore` = ?,
        `ACO` = null,
        `ACP` = null,
        `ACL` = null,
        `ACR` = null,
        `PO` = null,
        `PP` = null,
        `PL` = null,
        `PR` = null,
        `ScoringInProgress` = '1'
        where `ID` = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_bind_param($stmt, 'iiiis', 
        $organizerTricks,
        $organizerScore,
        $opponentTricks,
        $opponentScore,
        $gameID
    );
    
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
    mysqli_stmt_close($stmt);

    if ($opponentTricks == 0 && $organizerTricks == 0) {
      setDealInActive($conn, $gameID);
      $d = getDealer($conn, $gameID);
      $dealer = $d['Dealer'];

      if (strlen($dealer) == 1) {
        $dealer = getNextTurn($dealer);
        $turn = getNextTurn($dealer);
        
        $sql = "update `Game` set 
            `Dealer` = ?,
            `Lead` = null,
            `Turn` = ?,
            `OrganizerTrump` = null,
            `OpponentTrump` = null 
            where `ID` = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) { throw new Exception(mysqli_error($conn)); }
        
        mysqli_stmt_bind_param($stmt, 'sss', $dealer, $turn, $gameID);
        
        if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
        
        mysqli_stmt_close($stmt);
      } else {
        throw new Exception("Invalid dealer: '{$dealer}'");
      }
    } else {
      $sql = "update `Game` set `Lead` = null, `Turn` = ? where `ID` = ?";
      $stmt = mysqli_prepare($conn, $sql);
      if (!$stmt) { throw new Exception(mysqli_error($conn)); }
      
      mysqli_stmt_bind_param($stmt, 'ss', $winner, $gameID);
      
      if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
      
      mysqli_stmt_close($stmt);
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
  
  function getDealer($conn, $gameID) {
    $response = ['Dealer' => ''];

    $sql = "select `Dealer` from `Game` where `ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, 's', $gameID);
    
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_array($result)) {
      $response['Dealer'] = is_null($row['Dealer']) ? '' : $row['Dealer'];
    }
    
    mysqli_stmt_close($stmt);
        
    return $response;
  }
  
  function setDealInActive($conn, $gameID){
    $sql = "update `GameDeal` set `IsActive` = '0' where `GameID` = ? and `IsActive` = '1'";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, 's', $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    mysqli_stmt_close($stmt);
  }
?>