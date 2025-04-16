<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getThumbnailURL.php');

  trigger_error(basename(__FILE__)); // debug

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo 'Expecting request method: POST';
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r'])) {
    http_response_code(400); // Bad Request
    echo 'Missing or invalid authentication';
    exit;
  }

  $pid = $_POST['r'];
  $user = [];

  try {
    $sql = "select `ID`, `Name`, u.`ThumbnailPath`
            from `Player` p
            left join `UserProfile` u on u.`PlayerID` = p.`ID`
            where `ID` = ?";
    
    $stmt = mysqli_prepare($connection, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($connection)); }

    if (!mysqli_stmt_bind_param($stmt, "s", $pid)) { throw new Exception(mysqli_stmt_error($stmt)); }

    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);
    if (!$result) { throw new Exception(mysqli_stmt_error($stmt)); }

    while ($row = mysqli_fetch_assoc($result)) {
        $user['ID'] = $row['ID'];
        $user['Name'] = $row['Name'];
        $user['ThumbnailURL'] = is_null($row['ThumbnailPath']) ? '' : getThumbnailURL($row['ThumbnailPath']);
    }

    mysqli_stmt_close($stmt);

    http_response_code(200);
    echo json_encode($user);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo "Internal Server Error";
  }
  
?>