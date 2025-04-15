<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php');
  include('../svc/toEasternTime.php');
  
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

  $playerID = $_POST['r'];
  $games = [];
  $d = cutoffDate();

  try {

    $sql = "SELECT 
        `ID` AS `GameID`,
        `InsertDate`,
        `OrganizerScore`,
        `OpponentScore`
        FROM `Game` 
        WHERE `Organizer` = ? 
        AND `InsertDate` >= ?
        AND `GameFinishDate` IS NULL
        AND `GameEndDate` IS NULL";
    
    $stmt = mysqli_prepare($connection, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($connection)); }

    if (!mysqli_stmt_bind_param($stmt, "ss", $playerID, $d)) { throw new Exception(mysqli_stmt_error($stmt)); }

    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) { throw new Exception(mysqli_stmt_error($stmt)); }

    while ($row = mysqli_fetch_assoc($result)) {
      $et = toEasternTime($row['InsertDate']);
      $fet = $et->format('m-d-Y h:i a') . " et";
      $games[] = [
        $row['GameID'],
        $fet,
        $row['OrganizerScore'],
        $row['OpponentScore']
      ];
    }

    mysqli_stmt_close($stmt);
    http_response_code(200);
    echo json_encode($games);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    $response['ErrorMsg'] = 'Fatal error.';
    echo json_encode($response);
  }
?>