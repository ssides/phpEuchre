<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getNextTurn.php');

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication or gameID']);
    exit;
  }

  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  
  try {
    $sql = "select 
          g.`OrganizerScore`,
          g.`OpponentScore`,
          op.`Name` as `OName`,
          pp.`Name` as `PName`,
          lp.`Name` as `LName`,
          rp.`Name` as `RName`
      from `Game` g
      left join `UserProfile` ou on g.`Organizer` = ou.`PlayerID`
      join `Player` op on g.`Organizer` = op.`ID`
      left join `UserProfile` pu on g.`Partner` = pu.`PlayerID`
      join `Player` pp on g.`Partner` = pp.`ID`
      left join `UserProfile` lu on g.`Left` = lu.`PlayerID`
      join `Player` lp on g.`Left` = lp.`ID`
      left join `UserProfile` ru on g.`Right` = ru.`PlayerID`
      join `Player` rp on g.`Right` = rp.`ID` 
      where g.`ID` = ?";


    $stmt = mysqli_prepare($connection, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($connection)); }
    
    if (!mysqli_stmt_bind_param($stmt, 's', $gameID)) {
      throw new Exception(mysqli_stmt_error($stmt));
    }

    if (!mysqli_stmt_execute($stmt)) {
      throw new Exception(mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) { throw new Exception(mysqli_stmt_error($stmt)); }

    while ($row = mysqli_fetch_assoc($result)) {
      $response['OrganizerScore'] = $row['OrganizerScore'];
      $response['OpponentScore'] = $row['OpponentScore'];
      $response['OName'] = $row['OName'];
      $response['PName'] = $row['PName'];
      $response['LName'] = $row['LName'];
      $response['RName'] = $row['RName'];
    }

    mysqli_stmt_close($stmt);

    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    $response['ErrorMsg'] = 'An error occurred while getting winner.';
    echo json_encode($response);
  }

?>