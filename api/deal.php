<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST['r'];
      $deal = array();
      
      $deal = getRandomDealUnique($gameID);
      while (!isset($deal['DealID'])) {
        $deal = getRandomDealUnique($gameID);
      }
      
      $response['ErrorMsg'] .= insertDeal($gameID, $deal['DealID']);
      $response['ErrorMsg'] .= distributeCards($gameID, $deal);
      $response['ErrorMsg'] .= setCardFaceUp($gameID, $deal);
      $response['DealID'] = $deal['DealID'];
      
      http_response_code(200);
      
      echo json_encode($response);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

  // selects a deal at random. does not deal the same cards to the same player twice in a row.
  function getRandomDealUnique($gameID) {
    $deal = getRandomDeal($gameID);
    if (isset($deal['DealID'])) {
      $prevCards = getLastDeals($gameID, 2);
      $deal = checkUnique($deal, $prevCards);
    }
    return $deal;
  }
  
  // selects a deal at random. does not deal the same cards twice in the same game.
  function getRandomDeal($gameID) {
    global $response, $hostname, $username, $password, $dbname, $dealChoices;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);  // todo: use $connection.
    $deal = array();
    // each time a hand is dealt, the offset available will reduce from ($dealChoices - 1)
    // by one.  I'm not taking the time here to count the number of available choices.  
    // If mt_rand selects a number beyond the max offset, just call it again, until mt_rand()
    // chooses an acceptable offset.
    $r = mt_rand(0, $dealChoices - 1);
    
    $sql = "
      select d.`ID`,d.`Cards` 
      from `Deal` d
      left join `GameDeal` gd on d.`ID` = gd.`DealID` and gd.`GameID` = '{$gameID}'
      where `PurposeCode` = 'D' 
      and gd.`DealID` is null
      order by d.`ID`
      limit 1 offset {$r};";
      
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
      $response['ErrorMsg'] .= mysqli_error($conn);
    } else {
      while ($row = mysqli_fetch_array($result)) {
        $deal['DealID'] = $row['ID'];
        $deal['Cards'] = $row['Cards'];
      }
    }

    mysqli_close($conn);
    return $deal;
  }

  function insertDeal($gameID, $dealID) {
    global $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $ID = GUID();
    $errorMsg = "";
    
    $sql = "insert into `GameDeal` (`ID`,`DealID`,`GameID`,`IsActive`,`InsertDate`) values ('{$ID}','{$dealID}','{$gameID}','1',now())";
    if (mysqli_query($conn, $sql) === false) {
      $errorMsg = mysqli_error($conn);
    } 
    
    mysqli_close($conn);
    return $result;
  }

  function distributeCards($gameID, $deal) {
    global $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $result = "";
    
    $cards = $deal['Cards'];
    $result .= distributeCardsToPosition($conn, 'O', getOCards($cards), $gameID);
    $result .= distributeCardsToPosition($conn, 'P', getPCards($cards), $gameID);
    $result .= distributeCardsToPosition($conn, 'L', getLCards($cards), $gameID);
    $result .= distributeCardsToPosition($conn, 'R', getRCards($cards), $gameID);
    
    mysqli_close($conn);
    return $result;
  }

  function distributeCardsToPosition($conn, $position, $cards, $gameID) {
    $errorMsg = "";
    
    $ID = GUID();
    $c1 = substr($cards,0,3);
    $c2 = substr($cards,3,3);
    $c3 = substr($cards,6,3);
    $c4 = substr($cards,9,3);
    $c5 = substr($cards,12,3);
    
    $sql = "insert into `Play` (`ID`,`GameID`,`Position`,`CardID1`,`CardID2`,`CardID3`,`CardID4`,`CardID5`,`InsertDate`) 
            values ('{$ID}','{$gameID}','{$position}','{$c1}','{$c2}','{$c3}','{$c4}','{$c5}',now())";
    
    if (mysqli_query($conn, $sql) === false) {
      $errorMsg = mysqli_error($conn);
    }
    
    return $errorMsg;
  }
  
  function setCardFaceUp($gameID, $deal) {
    global $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    mysqli_query($conn, "START TRANSACTION;");

    $errorMsg = "";
    
    $up = substr($deal['Cards'],60,2);
    $sql = "update `Game` set `CardFaceUp`='{$up}',`OrganizerTrump` = null,`OpponentTrump` = null,`Lead` = null,`ACO` = null,`ACP` = null,`ACL` = null,`ACR` = null where `ID`='{$gameID}'";
    
    if (mysqli_query($conn, $sql) === false) {
      $errorMsg = mysqli_error($conn);
    }

    mysqli_query($conn, "COMMIT;");
    mysqli_close($conn);
    
    return $errorMsg;
  }

  // returns an unkeyed array of cards.
  function getLastDeals($gameID, $limit) {
    global $response, $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $deals = array();
    
    $sql = "
      select d.`Cards`
      from `GameDeal` gd
      left join `Deal` d on gd.`DealID` = d.`ID`
      where d.`PurposeCode` = 'D' and gd.`GameID` = '{$gameID}'
      order by gd.`InsertDate` desc
      limit ".$limit;

    $result = mysqli_query($conn, $sql);
    if ($result === false) {
      $response['ErrorMsg'] .= mysqli_error($conn);
    } else {
      while ($row = mysqli_fetch_array($result)) {
        array_push($deals, $row['Cards']);
      }
    }

    mysqli_close($conn);
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
  
  function getOCards($cards) {
    return substr($cards,0,15);
  }
  
  function getPCards($cards) {
    return substr($cards,15,15);
  }
  
  function getLCards($cards) {
    return substr($cards,30,15);
  }
  
  function getRCards($cards) {
    return substr($cards,45,15);
  }

?>