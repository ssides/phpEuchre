<?php 
header('Content-Type: application/json');

include_once('../../config/db.php');

if ($_SERVER["REQUEST_METHOD"] !== 'GET') {
  http_response_code(405); // Method Not Allowed
  echo json_encode(['ErrorMsg' => 'Expecting request method: GET']);
  exit;
}

if (!isset($_GET['term'])) {
    http_response_code(400);
    echo json_encode(['ErrorMsg' => 'Missing term parameter']);
    exit;
}

$response = ['ErrorMsg' => ''];

try {
  $term = trim($_GET['term'] ?? '');

  if (strlen($term) < 2) {
      echo json_encode(['names' => []]);
      exit;
  }

  $term = mysqli_real_escape_string($connection, $term);

  $sql = "select `Name` from `Player` 
          where `Name` like ? and `IsActive` = '1' 
          order by `Name` 
          limit 15";

  $stmt = mysqli_prepare($connection, $sql);
  $like = $term . '%';
  mysqli_stmt_bind_param($stmt, 's', $like);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $name);

  $names = [];
  while (mysqli_stmt_fetch($stmt)) {
      $names[] = $name;
  }

  mysqli_stmt_close($stmt);
  
  echo json_encode(['names' => $names]);

} catch (Exception $e) {
  trigger_error($e->getMessage(), E_USER_ERROR);

  http_response_code(500); // Internal Server Error
  $response['ErrorMsg'] = 'An error occurred while searching names.';
  echo json_encode($response);
}


?>