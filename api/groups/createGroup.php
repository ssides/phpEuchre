<?php 
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  include('../../svc/services.php'); // for GUID

  // I as positionID am acknowledging that I have seen the card played by playerID.
  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['groupName'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication or group name']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $groupName = $_POST['groupName'];
  $playerID = $_POST['r'];
  
  
  try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_connect($hostname, $username, $password, $dbname);

    mysqli_begin_transaction($conn);

    $existingGroup = getExistingGroupName($conn, $groupName);
    if (count($existingGroup) > 0) {
      $active = $existingGroup[0]['IsActive'] == '1' ? 'active' : 'inactive';
      $response['ErrorMsg'] = "Group '{$existingGroup[0]['Description']}' already exists as an {$active} group managed by {$existingGroup[0]['Manager']}.";
    } else {
      $groupID = createGroup($conn, $playerID, $groupName);
      setManager($conn, $groupID, $playerID);
    }
    
    mysqli_commit($conn);
    http_response_code(200);
    echo json_encode($response);
    mysqli_close($conn);

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
  
  function getExistingGroupName($conn, $groupName) {
    $response = [];
    $sql = "select 
      g.`Description`,g.`IsActive`,p.`Name` Manager
      from `Group` g
      join `Player` p on g.`ManagerID` = p.`ID`
      where `Description` = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $groupName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
      $response[] = ['Description' => $row['Description'], 'IsActive' => $row['IsActive'], 'Manager' => $row['Manager']];
    }
    
    mysqli_stmt_close($stmt);
    return $response;
  }
  
  function createGroup($conn, $playerID, $groupName) {
    $groupID = GUID();
    $sql = "insert into `Group` (ID,Description,ManagerID,IsActive,InsertDate) values (?,?,?,'1',now())";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $groupID, $groupName, $playerID);
    mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    return $groupID;
  }

  function setManager($conn, $groupID, $playerID) {
    $ID = GUID();
    $sql = "insert into `PlayerGroup` (ID,PlayerID,GroupID,IsActive,InsertDate) values (?,?,?,'1',now())";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $ID, $playerID, $groupID);
    mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
  }
?>