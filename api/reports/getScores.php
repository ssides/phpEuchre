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

  $response = [];
  $scores = [];
  $gameID = $_POST['gameID'];

  try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_connect($hostname, $username, $password, $dbname);

    $sql = "select `OpponentTrump`,`OrganizerTrump`,`Lead`,`CardO`,`CardL`,`CardP`,`CardR`,`OpponentScore`,`OrganizerScore`,`OpponentTricks`,`OrganizerTricks`,`CardFaceUp`,`Dealer`,`DealID`
      from `GamePlay` 
      where `GameID` = ?
      order by `InsertDate`";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $gameID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
      $r = [
        'OrganizerTrump' => $row['OrganizerTrump'],
        'OpponentTrump' => $row['OpponentTrump'],
        'Lead' => $row['Lead'],
        'CardO' => $row['CardO'],
        'CardL' => $row['CardL'],
        'CardP' => $row['CardP'],
        'CardR' => $row['CardR'],
        'OrganizerScore' => $row['OrganizerScore'],
        'OpponentScore' => $row['OpponentScore'],
        'OrganizerTricks' => $row['OrganizerTricks'],
        'OpponentTricks' => $row['OpponentTricks'],
        'CardFaceUp' => $row['CardFaceUp'],
        'DealID' => $row['DealID'],
        'Dealer' => $row['Dealer'],
      ];
      $scores[] = $r;
    }

    $response['Scores'] = $scores;
      
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