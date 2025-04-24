<?php 
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  
  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  
  try {
    $sql = "select `ID`,`Description` from `Group` g where g.`IsActive` = '1'";
    $stmt = mysqli_prepare($connection, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($connection)); }

    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);
        
    if (mysqli_num_rows($result) === 0) {
      $response['ErrorMsg'] = 'No groups are defined';
    } else {
      while ($row = mysqli_fetch_assoc($result)) {
        $response['Groups'][] = [$row['ID'], $row['Description']];
      }
    }

    mysqli_stmt_close($stmt);
    
    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo json_encode(['ErrorMsg' => 'An error occurred while updating the game.']);
  }
  
?>