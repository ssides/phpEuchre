<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  
  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo 'Expecting request method: POST';
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID'])) {
    http_response_code(400); // Bad Request
    echo 'Missing or invalid authentication or gameID';
    exit;
  }

  $gameID = $_POST['gameID'];
  $game = [];
  
  try {
    $stmt = mysqli_prepare($connection, "select `LeftJoinDate`, `RightJoinDate`, `PartnerJoinDate` from `Game` where `ID` = ?");
    if (!$stmt) { throw new Exception(mysqli_error($connection)); }
    
    if (!mysqli_stmt_bind_param($stmt, "s", $gameID)) { throw new Exception(mysqli_stmt_error($stmt)); }
    
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
      $game['LeftJoinDate'] = $row['LeftJoinDate'] ?? '';
      $game['RightJoinDate'] = $row['RightJoinDate'] ?? '';
      $game['PartnerJoinDate'] = $row['PartnerJoinDate'] ?? '';
    }

    mysqli_stmt_close($stmt);

    http_response_code(200);
    echo json_encode($game);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo "Internal server error";
  }

?>