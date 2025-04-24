<?php 
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $response['Groups'] = [];
  $playerID = $_POST['r'];
  
  try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_connect($hostname, $username, $password, $dbname);

    $sql = "select g.`Description`, g.`ManagerID`
      from `Group` g
      join `PlayerGroup` pg on g.`ID` = pg.`GroupID` and pg.`PlayerID` = ?
      where g.`IsActive` = '1' and pg.`IsActive` = '1'
      order by g.`Description`";
      
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $playerID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
      $response['Groups'][] = [
        'Description' => $row['Description'],
        'IsManager' => $row['ManagerID'] == $playerID ? 1 : 0
        ];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    if (isset($conn) && $conn) { mysqli_close($conn); }
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo json_encode(['ErrorMsg' => 'An error occurred while creating the group.']);
  }

?>