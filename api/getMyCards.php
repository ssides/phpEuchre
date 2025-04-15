<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID']) || !isset($_POST['positionID']) || strpos("OPLR", $_POST['positionID']) === false) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication, gameID, or positionID']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $positionID = $_POST['positionID'];
  $cards = [];
  
  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if (!$conn) {
    trigger_error("Database connection failed: " . mysqli_connect_error(), E_USER_ERROR);
    http_response_code(500);
    $response['ErrorMsg'] = "Internal server error.";
    echo json_encode($response);
    exit;
  }

  try {

    $sql = "select `CardID1`,`CardID2`,`CardID3`,`CardID4`,`CardID5` 
            from `Play` 
            where `GameID` = ? and `Position` = ?
            order by `InsertDate` desc
            limit 1";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }
    if (!mysqli_stmt_bind_param($stmt, "ss", $gameID, $positionID)) { throw new Exception(mysqli_stmt_error($stmt)); }
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
      $response['Cards'] = [
          'CardID1' => $row['CardID1'],
          'CardID2' => $row['CardID2'],
          'CardID3' => $row['CardID3'],
          'CardID4' => $row['CardID4'],
          'CardID5' => $row['CardID5']
      ];
    } else {
      $response['ErrorMsg'] = "No cards for gameID {$gameID} position {$positionID}";
    }

    mysqli_stmt_close($stmt);

    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    $response['ErrorMsg'] = 'An error occurred while getting my cards.';
    echo json_encode($response);
  }

  mysqli_close($conn);

?>