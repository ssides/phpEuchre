<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  // Select groups I'm not a member of and have no pending requests to join or the pending requests were all declined.
  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Missing or invalid authentication']);
    exit;
  }
  
  $pid = $_POST['r'];
  $groups = [];
  $response = [];
  
  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if (!$conn) {
    trigger_error("Database connection failed: " . mysqli_connect_error(), E_USER_ERROR);
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
    exit;
  }
  
  try {
    // `PlayerGroup` has an IsActive column that is only set by running SQL updates. 
    // Maybe that column should be dropped.  I'm not sure whether or not I should
    // include that column in these joins.
    
    $sql = "select g.`ID`, g.`Description` 
        from `Group` g
        left join `GroupRequest` gr on g.`ID` = gr.`GroupID` 
            and gr.`PlayerID` = ? 
            and (gr.`IsActive` = 'R' or gr.`IsActive` = 'A')
        left join `PlayerGroup` pgr on g.`ID` = pgr.`GroupID` 
            and pgr.`PlayerID` = ?
        where gr.`GroupID` is null 
            and pgr.`GroupID` is null 
            and g.`IsActive` = '1'";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }

    if (!mysqli_stmt_bind_param($stmt, 'ss', $pid, $pid)) {
        throw new Exception(mysqli_stmt_error($stmt));
    }

    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    mysqli_stmt_bind_result($stmt, $id, $description);
    while (mysqli_stmt_fetch($stmt)) {
        $groups[] = [$id, $description];
    }

    mysqli_stmt_close($stmt);

    $response['Groups'] = $groups;
    
    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Internal server error']);
  }

  mysqli_close($conn);

?>