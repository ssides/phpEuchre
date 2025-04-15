<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php');

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
  $invitations = [];
  $d = cutoffDate();

  try {
    $sql = "SELECT
            g.`ID` AS `GameID`,
            org.Name AS `OrganizerName`,
            CASE
                WHEN `Partner` = ? THEN 'Partner'
                WHEN `Left` = ? THEN 'Opponent Left'
                WHEN `Right` = ? THEN 'Opponent Right'
                ELSE 'Unknown'
            END AS `Position`
            FROM `Game` g
            JOIN `Player` org ON g.Organizer = org.ID
            WHERE ((`Partner` = ? AND PartnerJoinDate IS NULL)
                OR (`Left` = ? AND LeftJoinDate IS NULL)
                OR (`Right` = ? AND RightJoinDate IS NULL))
            AND g.`InsertDate` >= ?
            AND g.`GameEndDate` IS NULL
            ORDER BY g.`InsertDate` DESC";

    $stmt = mysqli_prepare($connection, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($connection)); }

    if (!mysqli_stmt_bind_param($stmt, "sssssss", $playerID, $playerID, $playerID, $playerID, $playerID, $playerID, $d)) {
      throw new Exception(mysqli_stmt_error($stmt));
    }

    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
      $invitations[] = [$row['GameID'], $row['OrganizerName'], $row['Position']];
    }

    mysqli_stmt_close($stmt);
    http_response_code(200);
    echo json_encode($invitations);
    
  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred']);
  }

  
?>