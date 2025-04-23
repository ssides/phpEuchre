<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

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

    $sql = "select g.`ID`,g.`Description`
      from `Group` g
      where g.`ManagerID` <> ?
      and g.`IsActive` = '1'
      and g.`ID` not in (select `GroupID`
        from `PlayerGroup`
        where `IsActive` = '1' and `PlayerID` = ?
        group by `GroupID`)
      and g.`ID` not in (select `GroupID`
        from `GroupRequest`
        where `IsActive` in ('A', 'R') and `PlayerID` = ?
        group by `GroupID`)
        order by g.`Description`";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $playerID, $playerID, $playerID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
      $response['Groups'][] = ['ID' => $row['ID'], 'Description' => $row['Description']];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo json_encode(['ErrorMsg' => 'An error occurred while creating the group.']);
  }

?>