<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getThumbnailURL.php');

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication or gameID.']);
    exit;
  }

  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $playerID = $_POST['r'];
  $game = [];

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if (!$conn) {
    trigger_error("Database connection failed: " . mysqli_connect_error(), E_USER_ERROR);
    http_response_code(500);
    $response['ErrorMsg'] = "Internal server error.";
    echo json_encode($response);
    exit;
  }

  try {
    $sql = "SELECT 
            `Organizer`, `Partner`, `Left`, `Right`, `Dealer`, `Turn`, `Lead`, `CardFaceUp`,
            `OrganizerTrump`, `OrganizerTricks`, `OrganizerScore`, `OpponentTrump`, 
            `OpponentTricks`, `OpponentScore`, `PlayTo`, `ACO`, `ACP`, `ACR`, `ACL`, 
            `PO`, `PP`, `PL`, `PR`, `ScoringInProgress`, `Speed`,
            ou.`ThumbnailPath` `OThumbnailPath`, op.`Name` `OName`,
            pu.`ThumbnailPath` `PThumbnailPath`, pp.`Name` `PName`,
            lu.`ThumbnailPath` `LThumbnailPath`, lp.`Name` `LName`,
            ru.`ThumbnailPath` `RThumbnailPath`, rp.`Name` `RName`,
            `GameStartDate`, `GameFinishDate`, `GameEndDate`, gd.`DealID`
        FROM `Game` g
        LEFT JOIN (SELECT `DealID`, `GameID` FROM `GameDeal` WHERE `GameID` = ? AND `IsActive` = '1') gd ON g.`ID` = gd.`GameID`
        LEFT JOIN `UserProfile` ou ON g.`Organizer` = ou.`PlayerID`
        JOIN `Player` op ON g.`Organizer` = op.`ID`
        LEFT JOIN `UserProfile` pu ON g.`Partner` = pu.`PlayerID`
        JOIN `Player` pp ON g.`Partner` = pp.`ID`
        LEFT JOIN `UserProfile` lu ON g.`Left` = lu.`PlayerID`
        JOIN `Player` lp ON g.`Left` = lp.`ID`
        LEFT JOIN `UserProfile` ru ON g.`Right` = ru.`PlayerID`
        JOIN `Player` rp ON g.`Right` = rp.`ID`
        WHERE g.`ID` = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
      throw new Exception(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ss", $gameID, $gameID);
    if (!mysqli_stmt_execute($stmt)) {
      throw new Exception(mysqli_error($conn));
    }

    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
      $game = [
          'Organizer' => $row['Organizer'],
          'Partner' => $row['Partner'],
          'Left' => $row['Left'],
          'Right' => $row['Right'],
          'Dealer' => $row['Dealer'],
          'Turn' => $row['Turn'] ?? '',
          'Lead' => $row['Lead'] ?? '',
          'CardFaceUp' => $row['CardFaceUp'] ?? '',
          'OrganizerTrump' => $row['OrganizerTrump'] ?? '',
          'OrganizerTricks' => $row['OrganizerTricks'] ?? '',
          'OrganizerScore' => $row['OrganizerScore'],
          'OpponentTrump' => $row['OpponentTrump'] ?? '',
          'OpponentTricks' => $row['OpponentTricks'] ?? '',
          'OpponentScore' => $row['OpponentScore'],
          'PlayTo' => $row['PlayTo'],
          'ACO' => $row['ACO'] ?? '',
          'ACP' => $row['ACP'] ?? '',
          'ACR' => $row['ACR'] ?? '',
          'ACL' => $row['ACL'] ?? '',
          'PO' => $row['PO'] ?? '',
          'PP' => $row['PP'] ?? '',
          'PL' => $row['PL'] ?? '',
          'PR' => $row['PR'] ?? '',
          'ScoringInProgress' => $row['ScoringInProgress'],
          'Speed' => $row['Speed'],
          'OThumbnailPath' => $row['OThumbnailPath'],
          'OThumbnailURL' => $row['OThumbnailPath'] ? getThumbnailURL($row['OThumbnailPath']) : '',
          'OName' => $row['OName'],
          'PThumbnailURL' => $row['PThumbnailPath'] ? getThumbnailURL($row['PThumbnailPath']) : '',
          'PName' => $row['PName'],
          'LThumbnailURL' => $row['LThumbnailPath'] ? getThumbnailURL($row['LThumbnailPath']) : '',
          'LName' => $row['LName'],
          'RThumbnailURL' => $row['RThumbnailPath'] ? getThumbnailURL($row['RThumbnailPath']) : '',
          'RName' => $row['RName'],
          'GameStartDate' => $row['GameStartDate'],
          'GameFinishDate' => $row['GameFinishDate'] ?? '',
          'GameEndDate' => $row['GameEndDate'] ?? '',
          'DealID' => $row['DealID'] ?? ''
      ];
    }

    $response['Game'] = $game;

    mysqli_stmt_close($stmt);
    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    $response['ErrorMsg'] = 'An error occurred while getting the game state.';
    echo json_encode($response);
  }
  
  mysqli_close($conn);

?>