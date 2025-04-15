<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  // Select group join requests for the group I am logged in to.
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

  $gid = $_POST['groupID'];
  $requests = [];
  $response = [];

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if (!$conn) {
    trigger_error("Database connection failed: " . mysqli_connect_error(), E_USER_ERROR);
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
    exit;
  }
  
  try {
    
    $sql = "select p.`ID`, p.`Name` 
            from `GroupRequest` gr 
            join `Player` p on gr.`PlayerID` = p.`ID`
            where gr.`GroupID` = ? and gr.`IsActive` = 'R'";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }
    
    if (!mysqli_stmt_bind_param($stmt, 's', $gid)) {
        throw new Exception(mysqli_stmt_error($stmt));
    }
    
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
    
    mysqli_stmt_bind_result($stmt, $id, $name);
    while (mysqli_stmt_fetch($stmt)) {
      $requests[] = array($id, $name); // push
    }
    
    mysqli_stmt_close($stmt);
    
    $response['Requests'] = $requests;
    http_response_code(200);
    echo json_encode($response);
            
  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An error occurred']);
  }

  mysqli_close($conn);

?>