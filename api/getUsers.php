<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getThumbnailURL.php');

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['groupID'])) {
    http_response_code(400); // Bad Request
    echo 'Missing or invalid authentication or groupID';
    exit;
  }

  $response = [];
  $users = [];
  $pid = $_POST['r'];
  $gid = $_POST['groupID'];

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if (!$conn) {
    trigger_error("Database connection failed: " . mysqli_connect_error(), E_USER_ERROR);
    http_response_code(500);
    $response['ErrorMsg'] = "Internal server error.";
    echo json_encode($response);
    exit;
  }

  try {
    $sql = "select p.`ID`, substr(`Name`, 1, 8) `Name`, u.`ThumbnailPath`
        from `Player` p
        join `PlayerGroup` pg on p.`ID` = pg.`PlayerID` and pg.`GroupID` = ? and pg.`IsActive` = '1'
        left join `UserProfile` u on u.`PlayerID` = p.`ID`
        where p.`ID` <> ? and p.`IsActive` = '1'
        order by `Name`";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }

    if (!mysqli_stmt_bind_param($stmt, 'ss', $gid, $pid)) { throw new Exception(mysqli_stmt_error($stmt)); }

    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    mysqli_stmt_bind_result($stmt, $id, $name, $thumbnail);
    while (mysqli_stmt_fetch($stmt)) {
      $tnURL = is_null($thumbnail) ? '' : getThumbnailURL($thumbnail);
      $users[] = array($id, $name, $tnURL);
    }

    mysqli_stmt_close($stmt);
    
    $response['Users'] = $users;
    
    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    $response['ErrorMsg'] = 'An error occurred while getting users.';
    echo json_encode($response);
  }

  mysqli_close($conn);

?>