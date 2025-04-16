<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php');

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo 'Expecting request method: POST';
    exit;
  }

  if (!isset($_POST['r']) 
    || !isAuthenticated($_POST['r']) 
    || !isset($_POST['groupID']) 
    || empty($_POST['groupID']) 
    || !isset($_POST['playerID']) 
    || empty($_POST['playerID'])
    || !isset($_POST['code']) 
    || empty($_POST['code'])
  ) {
    http_response_code(400); // Bad Request
    echo 'One or more request parameters are missing or invalid';
    exit;
  }

  $gid = $_POST['groupID'];
  $pid = $_POST['playerID'];
  $code = $_POST['code'];
  $guid = GUID();

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
    // Update GroupRequest
    $stmt = mysqli_prepare($conn, "update `GroupRequest` set `IsActive` = ? where `PlayerID` = ? and `GroupID` = ?");
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_bind_param($stmt, 'sss', $code, $pid, $gid);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
    mysqli_stmt_close($stmt);

    // Insert into PlayerGroup if code is 'A'
    if ($code === 'A') {
      $stmt = mysqli_prepare($conn, 
          "insert into `PlayerGroup` (id, playerid, groupid, isactive, insertdate) 
          values (?, ?, ?, '1', now())"
      );
      if (!$stmt) { throw new Exception(mysqli_error($conn)); }
      
      mysqli_stmt_bind_param($stmt, 'sss', $guid, $pid, $gid);
      if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
      mysqli_stmt_close($stmt);
    }

    mysqli_commit($conn);
    http_response_code(200);
    echo "";

  } catch (Exception $e) {
    mysqli_rollback($conn);
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500);
    echo "Internal server error.";
  }

  mysqli_close($conn);

?>