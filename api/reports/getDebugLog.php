<?php
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  
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
  $response = [];
  $log = [];
  
  try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_connect($hostname, $username, $password, $dbname);

    $stmt = mysqli_prepare($conn, "select `DealID`, `PlayerID`, `GameControllerState`, `InsertDate`, `Message`, `OpponentScore`, `OpponentTricks`, `OrganizerScore`, `OrganizerTricks`, `PositionID`, `Dealer`, `Turn`, `CardFaceUp`, `ACO`, `ACP`, `ACL`, `ACR`, `PO`, `PP`, `PL`, `PR`
      from `GameControllerLog` 
      where `GameID` = ? 
      order by `InsertDate`");
      
    mysqli_stmt_bind_param($stmt, "s", $gameID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
      $r = [
        'OrganizerScore' => $row['OrganizerScore'],
        'OpponentScore' => $row['OpponentScore'],
        'OrganizerTricks' => $row['OrganizerTricks'],
        'OpponentTricks' => $row['OpponentTricks'],
        'DealID' => $row['DealID'] ?? '',
        'PlayerID' => $row['PlayerID'],
        'GameControllerState' => $row['GameControllerState'],
        'InsertDate' => $row['InsertDate'],
        'Message' => $row['Message'] ?? '',
        'PositionID' => $row['PositionID'],
        'Dealer' => $row['Dealer'] ?? '' ,
        'Turn' => $row['Turn'] ?? '' ,
        'CardFaceUp' => $row['CardFaceUp'] ?? '' ,
        'ACO' => $row['ACO'] ?? '' ,
        'ACP' => $row['ACP'] ?? '' ,
        'ACL' => $row['ACL'] ?? '' ,
        'ACR' => $row['ACR'] ?? '' ,
        'PO' => $row['PO'] ?? '' ,
        'PP' => $row['PP'] ?? '' ,
        'PL' => $row['PL'] ?? '' ,
        'PR' => $row['PR'] ?? '' ,
      ];
      $log[] = $r;
    }
    
    $response['Log'] = $log;
    
    http_response_code(200);
    echo json_encode($response);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    echo 'An error occurred while getting game data.';
  }
  

?>