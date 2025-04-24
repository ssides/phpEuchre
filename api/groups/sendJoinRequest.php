<?php 
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  include('../../svc/services.php'); // for GUID

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['groupID'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication or groupID']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $playerID = $_POST['r'];
  $groupID = $_POST['groupID'];
  
  try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_connect($hostname, $username, $password, $dbname);

    mysqli_begin_transaction($conn);

    $existingGroup = getExistingJoinRequest($conn, $playerID, $groupID);
    
    if (count($existingGroup) == 0) {
      $smt = mysqli_prepare($conn, "insert into `GroupRequest` (`ID`,`PlayerID`,`GroupID`,`IsActive`,`InsertDate`) values (?, ?, ?, 'R', now())");
      mysqli_stmt_bind_param($smt, 'sss', GUID(), $playerID, $groupID);
      mysqli_stmt_execute($smt);
      mysqli_stmt_close($smt);
    }
    
    mysqli_commit($conn);
    mysqli_close($conn);
    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    if (isset($conn) && $conn) { 
      mysqli_rollback($conn);
      mysqli_close($conn); 
    }
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo json_encode(['ErrorMsg' => 'An error occurred while creating the group.']);
  }
  
  // functions
  
  function getExistingJoinRequest($conn, $playerID, $groupID) {
    $response = [];
    $sql = "select `ID` GroupRequestID
      from `GroupRequest`
      where `PlayerID` = ? and `GroupID` = ?";
      
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $playerID, $groupID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
      $response[] = ['GroupRequestID' => $row['GroupRequestID']];
    }
    
    mysqli_stmt_close($stmt);
    return $response;
  }
  
?>