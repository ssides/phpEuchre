<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID

  if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['ErrorMsg' => 'Expecting request method: POST']);
    exit;
  }

  if (!isset($_POST['r']) || !isAuthenticated($_POST['r']) || !isset($_POST['gameID'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['ErrorMsg' => 'Missing or invalid authentication, gameID']);
    exit;
  }
  
  $response = ['ErrorMsg' => ''];
  $gameID = $_POST['gameID'];
  $playerID = $_POST['r'];
  $deal = [];

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if (!$conn) {
    trigger_error("Database connection failed: " . mysqli_connect_error(), E_USER_ERROR);
    http_response_code(500);
    $response['ErrorMsg'] = "Internal server error.";
    echo json_encode($response);
    exit;
  }

  mysqli_begin_transaction($conn);

  try {

    $deal = getRandomDealUnique($conn, $gameID);
    while (!isset($deal['DealID'])) {
      $deal = getRandomDealUnique($conn, $gameID);
    }

    insertDeal($conn, $gameID, $deal['DealID']);
    distributeCards($conn, $gameID, $deal);
    setCardFaceUp($conn, $gameID, $deal);
    $response['DealID'] = $deal['DealID'];

    mysqli_commit($conn);
    http_response_code(200);
    echo json_encode($response);

  } catch (Exception $e) {
    mysqli_rollback($conn);
    trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
    http_response_code(500); // Internal Server Error
    $response['ErrorMsg'] = 'An error occurred while dealing.';
    echo json_encode($response);
  }
  
  mysqli_close($conn);

  // ------ Functions
  //
  // selects a deal at random. does not deal the same cards to the same player twice in a row.
  function getRandomDealUnique($conn, $gameID) {
    $deal = getRandomDeal($conn, $gameID);
    if (isset($deal['DealID'])) {
    $prevCards = getLastDeals($conn, $gameID, 2);
    $deal = checkUnique($deal, $prevCards);
    }
    return $deal;
  }

  // selects a deal at random. does not deal the same cards twice in the same game.
  function getRandomDeal($conn, $gameID) {
    global $dealChoices;
    $deal = [];
    $r = mt_rand(0, $dealChoices - 1);
    
    $sql = "
        SELECT d.`ID`, d.`Cards`
        FROM `Deal` d
        LEFT JOIN `GameDeal` gd ON d.`ID` = gd.`DealID` AND gd.`GameID` = ?
        WHERE `PurposeCode` = 'D'
        AND gd.`DealID` IS NULL
        ORDER BY d.`ID`
        LIMIT 1 OFFSET ?";
        
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($conn));  }
    
    mysqli_stmt_bind_param($stmt, "si", $gameID, $r);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result === false) {
      throw new Exception(mysqli_error($conn));
    } else {
      while ($row = mysqli_fetch_array($result)) {
        $deal['DealID'] = $row['ID'];
        $deal['Cards'] = $row['Cards'];
      }
    }
    
    mysqli_stmt_close($stmt);
    return $deal;
  }

  function getLastDeals($gameID, $limit) {
    $deals = [];

    $sql = "
      SELECT d.`Cards`
      FROM `GameDeal` gd
      LEFT JOIN `Deal` d ON gd.`DealID` = d.`ID`
      WHERE d.`PurposeCode` = 'D' AND gd.`GameID` = ?
      ORDER BY gd.`InsertDate` DESC
      LIMIT ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
      throw new Exception(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "si", $gameID, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result === false) {
      throw new Exception(mysqli_error($conn));
    } else {
      while ($row = mysqli_fetch_array($result)) {
        array_push($deals, $row['Cards']);
      }
    }

    mysqli_stmt_close($stmt);
    return $deals;
  }

  // returns $result['DealID'] unset if the cards for one or more players have been dealt recently.
  function checkUnique($deal, $prevCards) {
    $result = $deal;
    $dealt = $deal['Cards'];
    
    foreach ($prevCards as $c) {
      if (getOCards($dealt) == getOCards($c)
          || getPCards($dealt) == getPCards($c)
          || getLCards($dealt) == getLCards($c)
          || getRCards($dealt) == getRCards($c)
      ) {
          unset($result['DealID']);
          unset($result['Cards']);
      }
    }
    
    return $result;
  }

  function insertDeal($conn, $gameID, $dealID) {
      $ID = GUID();
      $errorMsg = "";
      
      $sql = "INSERT INTO `GameDeal` (`ID`, `DealID`, `GameID`, `IsActive`, `InsertDate`) VALUES (?, ?, ?, '1', NOW())";
      $stmt = mysqli_prepare($conn, $sql);
      if ($stmt === false) { throw new Exception(mysqli_error($conn));  }
      
      mysqli_stmt_bind_param($stmt, "sss", $ID, $dealID, $gameID);
      if (mysqli_stmt_execute($stmt) === false) { throw new Exception(mysqli_error($conn)); }
      
      mysqli_stmt_close($stmt);
      return $errorMsg;
  }

  function distributeCards($conn, $gameID, $deal) {
    $cards = $deal['Cards'];
    $result .= distributeCardsToPosition($conn, 'O', getOCards($cards), $gameID);
    $result .= distributeCardsToPosition($conn, 'P', getPCards($cards), $gameID);
    $result .= distributeCardsToPosition($conn, 'L', getLCards($cards), $gameID);
    $result .= distributeCardsToPosition($conn, 'R', getRCards($cards), $gameID);
  }

  function distributeCardsToPosition($conn, $position, $cards, $gameID) {
    $ID = GUID();
    $c1 = substr($cards, 0, 3);
    $c2 = substr($cards, 3, 3);
    $c3 = substr($cards, 6, 3);
    $c4 = substr($cards, 9, 3);
    $c5 = substr($cards, 12, 3);
    
    $sql = "INSERT INTO `Play` (`ID`, `GameID`, `Position`, `CardID1`, `CardID2`, `CardID3`, `CardID4`, `CardID5`, `InsertDate`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_bind_param($stmt, "ssssssss", $ID, $gameID, $position, $c1, $c2, $c3, $c4, $c5);
    if (mysqli_stmt_execute($stmt) === false) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_close($stmt);
  }
  
  function setCardFaceUp($conn, $gameID, $deal) {
    $up = substr($deal['Cards'], 60, 2);

    $sql = "UPDATE `Game` SET `CardFaceUp`=?, `OrganizerTrump`=NULL, `OpponentTrump`=NULL, `Lead`=NULL, `ACO`=NULL, `ACP`=NULL, `ACL`=NULL, `ACR`=NULL WHERE `ID`=?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt, "ss", $up, $gameID);
    if (mysqli_stmt_execute($stmt) === false) { throw new Exception(mysqli_error($conn)); } 

    mysqli_stmt_close($stmt);
  }

  function getOCards($cards) {
    return substr($cards, 0, 15);
  }

  function getPCards($cards) {
    return substr($cards, 15, 15);
  }

  function getLCards($cards) {
    return substr($cards, 30, 15);
  }

  function getRCards($cards) {
    return substr($cards, 45, 15);
  }

?>