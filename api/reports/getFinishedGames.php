<?php
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  
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

  $response = ['ErrorMsg' => ''];
  $games = [];
  $d = cutoffDate();
  $response['CutoffDate'] = $d;
  
  try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    
    $sql = " select 
        g.`GameFinishDate`
        ,op.`Name` `OName`
        ,pp.`Name` `PName`
        ,g.`OrganizerScore`
        ,lp.`Name` `LName`
        ,rp.`Name` `RName`
        ,g.`OpponentScore`
      from `Game` g
      left join `UserProfile` ou on g.`Organizer` = ou.`PlayerID`
      join `Player` op on g.`Organizer` = op.`ID`
      left join `UserProfile` pu on g.`Partner` = pu.`PlayerID`
      join `Player` pp on g.`Partner` = pp.`ID`
      left join `UserProfile` lu on g.`Left` = lu.`PlayerID`
      join `Player` lp on g.`Left` = lp.`ID`
      left join `UserProfile` ru on g.`Right` = ru.`PlayerID`
      join `Player` rp on g.`Right` = rp.`ID`
      where g.`GameStartDate` is not null
      and g.`GameFinishDate` is not null
      and g.`GameFinishDate` > ?
      order by g.`GameFinishDate`
      ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $d);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
      $r = [
        'GameFinishDate' => $row['GameFinishDate'],
        'OName' => $row['OName'],
        'PName' => $row['PName'],
        'OrganizerScore' => $row['OrganizerScore'],
        'LName' => $row['LName'],
        'RName' => $row['RName'],
        'OpponentScore' => $row['OpponentScore'],
      ];
      $games[] = $r;
    }
    
    $response['Games'] = $games;
    http_response_code(200);
    echo json_encode($response);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo 'An error occurred while getting game data.';
  }

  function cutoffDate() {
    $now = new DateTime();
    $now->sub(new DateInterval('P6M'));
    return $now->format('Y-m-d');
  }

?>