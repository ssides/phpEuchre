<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php');

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo "Expecting request method: POST";
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID']) || !isset($_POST['positionID']) || strpos("OPLR", $_POST['positionID']) === false) {
    http_response_code(400); // Bad Request
      echo "ID invalid or missing.";
    exit;
  }
  
  $playerID = $_POST['r'];
  $games = [];
  $d = cutoffDate();
  
  try {
    $sql = "select
        g.`ID` as GameID,
        case
            when `Organizer` = ? then 'You'
            else org.Name
        end as OrganizerName,
        case
            when `Partner` = ? then 'Partner'
            when `Left` = ? then 'Opponent Left'
            when `Right` = ? then 'Opponent Right'
            when `Organizer` = ? then 'Organizer'
            else 'Unknown'
        end as Position
    from `Game` g
    join `Player` org on g.Organizer = org.ID
    where ((`Partner` = ? and PartnerJoinDate is not null and PartnerJoinDate >= ?)
        or (`Left` = ? and LeftJoinDate is not null and LeftJoinDate >= ?)
        or (`Right` = ? and RightJoinDate is not null and RightJoinDate >= ?)
        or (`Organizer` = ? and GameStartDate is not null and GameStartDate >= ?))
        and g.`GameFinishDate` is null
        and g.`GameEndDate` is null
    order by g.`InsertDate` desc";

    $stmt = mysqli_prepare($connection, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($connection)); }

    $result = mysqli_stmt_bind_param($stmt, "sssssssssssss",
        $playerID, $playerID, $playerID, $playerID, $playerID,
        $playerID, $d, $playerID, $d, $playerID, $d, $playerID, $d
    );

    if (!$result) { throw new Exception(mysqli_stmt_error($stmt)); }

    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) { throw new Exception(mysqli_stmt_error($stmt)); }

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $games[] = [$row['GameID'], $row['OrganizerName'], $row['Position']];
    }

    mysqli_stmt_close($stmt);

    http_response_code(200);
    echo json_encode($games);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo 'An error occurred while getting rejoinable games.';
  }



?>