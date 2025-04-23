<?php
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  include('../../svc/services.php');
  
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

  try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    
    $sql = "select 
        g.`ID` GameID,
        ACP, ACR, ACL,
        g.`GameStartDate`,
        g.`PartnerJoinDate`,
        g.`LeftJoinDate`,
        g.`RightJoinDate`,
        g.`PartnerJoinDate` is not null and g.`PartnerJoinDate` >= ? `PCanRejoin`,
        g.`LeftJoinDate` is not null and g.`LeftJoinDate` >= ? `LCanRejoin`,
        g.`RightJoinDate` is not null and g.`RightJoinDate` >= ? `RCanRejoin`,
        g.`GameStartDate` is not null and g.`GameStartDate` >= ? `OCanRejoin`,
        g.`GameFinishDate`,
        g.`GameFinishDate` is not null `GameIsFinished`,
        op.`Name` `OName`,
        pp.`Name` `PName`,
        lp.`Name` `LName`,
        rp.`Name` `RName`,
        ? `CutoffDate`
    from `Game` g
    left join `UserProfile` ou ON g.`Organizer` = ou.`PlayerID`
    join `Player` op ON g.`Organizer` = op.`ID`
    left join `UserProfile` pu ON g.`Partner` = pu.`PlayerID`
    join `Player` pp ON g.`Partner` = pp.`ID`
    left join `UserProfile` lu ON g.`Left` = lu.`PlayerID`
    join `Player` lp ON g.`Left` = lp.`ID`
    left join `UserProfile` ru ON g.`Right` = ru.`PlayerID`
    join `Player` rp ON g.`Right` = rp.`ID`
    where g.`GameStartDate` is not null
    order by g.`GameStartDate` desc
    limit 100";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $d, $d, $d, $d, $d);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
      $r = [
        'GameID' => $row['GameID'],
        'PCanRejoin' => $row['PCanRejoin'],
        'OCanRejoin' => $row['OCanRejoin'],
        'LCanRejoin' => $row['LCanRejoin'],
        'RCanRejoin' => $row['RCanRejoin'],
        'CutoffDate' => $row['CutoffDate'],
        'GameFinishDate' => $row['GameFinishDate'],
        'GameStartDate' => $row['GameStartDate'],
        'OName' => $row['OName'],
        'PName' => $row['PName'],
        'LName' => $row['LName'],
        'RName' => $row['RName'],
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
    echo json_encode(['ErrorMsg' => 'An error occurred while getting game data.']);
  }

?>