<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getDealServices.php');
  
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
  
  $card = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $playerID = $_POST['r'];

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if (!$conn) {
    trigger_error("Database connection failed: " . mysqli_connect_error(), E_USER_ERROR);
    http_response_code(500);
    $card['ErrorMsg'] = "Internal server error.";
    echo json_encode($card);
    exit;
  }

  mysqli_begin_transaction($conn);

  try {
    $d = getDealID($conn, $gameID);
    $errorMsg .= $d['ErrorMsg'];
    if (strlen($d['DealID']) > 0) {
      $c = getCurrentFDeal($conn, $gameID);
      $errorMsg .= $c['ErrorMsg'];
      $card['ID'] = substr($c['Cards'], $c['Index'], 2);
      $card['Position'] = $c['Position'];
    } else {
      $card['ID'] = '';
      $card['Position'] = '';
      $card['ErrorMsg'] = "Invalid DealID.";
    }
    
    mysqli_commit($conn);
    http_response_code(200);
    echo json_encode($card);
    
  } catch (Exception $e) {
    mysqli_rollback($conn);
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    $card['ErrorMsg'] = 'An error occurred while getting the current start card.';
    echo json_encode($card);
  }

  mysqli_close($conn);

?>