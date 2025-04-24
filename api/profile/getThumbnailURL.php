<?php 
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  include('../../svc/thumbnailServices.php');
  include('../../svc/getThumbnailURL.php');

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
  
  $response = ['ErrorMsg' => '', 'ThumbnailURL' => ''];
  $playerID = $_POST['r'];
  
  try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_connect($hostname, $username, $password, $dbname);

    $summary = getUserProfileSummary($conn, $playerID);
    $response['ThumbnailURL'] = empty($summary['thumbnailPath']) ? '' : getThumbnailURL($summary['thumbnailPath']);
    
    http_response_code(200);
    echo json_encode($response);
    mysqli_close($conn);

  } catch (Exception $e) {
    if (isset($conn) && $conn) { mysqli_close($conn); }
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo json_encode(['ErrorMsg' => 'An error occurred while creating the group.']);
  }

?>