<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID
  include('../svc/getNextPosition.php');
  include('../svc/getDealServices.php');
  
  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication, or gameID']);
    exit;
  }
  
  $gameID = $_POST['gameID'];
  $playerID = $_POST['r'];
  $card = ['ErrorMsg' => ''];

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if (!$conn) {
    trigger_error("Database connection failed: " . mysqli_connect_error(), E_USER_ERROR);
    http_response_code(500);
    $card['ErrorMsg'] = "Internal server error.";
    echo json_encode($card);
    exit;
  }

  mysqli_begin_transaction($conn);

  try {
    
    $f = getDealID($conn, $gameID);
    if (empty($f['DealID'])) {
      $d = getRandomFDeal($conn);
      insertFDeal($conn, $gameID, $d['DealID']);
      $card['ID'] = substr($d['Cards'], 0, 2);
      $card['Position'] = 'L';
    } else {
      $c = getCurrentFDeal($conn, $gameID);
      if ($c['Cards'][$c['Index']] != 'J') {
        $c['Index'] += 3;
        $card['ID'] = substr($c['Cards'], $c['Index'], 2);
        $card['Position'] = getNextPosition($c['Position']);
        updateFDeal($conn, $gameID, $c['Index'], $card['Position']);
      } else {
        $card['ID'] = substr($c['Cards'], $c['Index'], 2);
        $card['Position'] = $c['Position'];
      }
    }
            
    mysqli_commit($conn);
    http_response_code(200);
    echo json_encode($card);
    
  } catch (Exception $e) {
    mysqli_rollback($conn);
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    $card['ErrorMsg'] = 'An error occurred while getting the next start card.';
    echo json_encode($card);
  }

  mysqli_close($conn);

  // functions
  
  function getRandomFDeal($conn) {
    global $firstJackChoices;
    $fdeal = ['DealID' => '', 'Cards' => ''];

    $r = mt_rand(0, $firstJackChoices - 1);
    $stmt = mysqli_prepare($conn, "SELECT `ID`, `Cards` FROM `Deal` WHERE `PurposeCode`='J' ORDER BY `ID` LIMIT 1 OFFSET ?");
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, "i", $r);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $fdeal['DealID'] = $row['ID'];
        $fdeal['Cards'] = $row['Cards'];
    }

    mysqli_stmt_close($stmt);

    return $fdeal;
  }
  
  function insertFDeal($conn, $gameID, $dealID) {
    $ID = GUID();
    $stmt = mysqli_prepare($conn, "INSERT INTO `GameDeal` (`ID`, `DealID`, `GameID`, `InsertDate`) VALUES (?, ?, ?, NOW())");
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, "sss", $ID, $dealID, $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "UPDATE `Game` SET `FirstJackIndex`=0, `FirstJackPosition`='L' WHERE `ID`=?");
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, "s", $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    mysqli_stmt_close($stmt);
  }

  function updateFDeal($conn, $gameID, $ix, $position) {
    $stmt = mysqli_prepare($conn, "UPDATE `Game` SET `FirstJackIndex`=?, `FirstJackPosition`=? WHERE `ID`=?");
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, "iss", $ix, $position, $gameID);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    mysqli_stmt_close($stmt);
}

?>