<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo "Expecting request method: POST";
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r'])  || !isset($_POST['identifier']) || !isset($_POST['player']) || !isset($_POST['gameID'])) {
    http_response_code(400); // Bad Request
    echo "One or more request parameters are invalid or missing.";
    exit;
  }

  $id = $_POST['identifier'];  // 'partner', 'left' or 'right'
  $playerID = $_POST['player'];
  $gameID = $_POST['gameID'];
  
  mysqli_begin_transaction($connection);

  try {
    $sql = '';
    $date_column = '';

    switch ($id) {
      case 'partner':
        $date_column = 'partnerinvitedate';
        break;
      case 'left':
        $date_column = 'leftinvitedate';
        break;
      case 'right':
        $date_column = 'rightinvitedate';
        break;
      default:
        throw new Exception('Invalid identifier');
    }

    $sql = "update `Game` set `{$id}` = ?, `{$date_column}` = now() where id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($connection)); }

    $result = mysqli_stmt_bind_param($stmt, 'ss', $playerID, $gameID);
    if ($result === false) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_execute($stmt);
    if ($result === false) { throw new Exception(mysqli_stmt_error($stmt)); }

    mysqli_stmt_close($stmt);

    mysqli_commit($connection);
    http_response_code(200);
    echo 'OK';

  } catch (Exception $e) {
    mysqli_rollback($connection);
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo 'An error occurred while inviting a player.';
  }

?>