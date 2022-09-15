<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getDealID.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $errorMsg = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST[$cookieName];
      $card = array();
      
      $dealID = getDealID($gameID);
      if (!is_null($dealID)) {
        $c = getCurrentFDeal($gameID);
        $card['ID'] = substr($c['Cards'], $c['Index'], 2);
        $card['Position'] = $c['Position'];
      } else {
        $card['ID'] = '';
        $card['Position'] = '';
        $errorMsg = 'No dealID';
      }
      
      $card['ErrorMsg'] = $errorMsg;
      
      http_response_code(200);
      echo json_encode($card);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

  function updateFDeal($gameID, $ix, $position) {
    global $errorMsg, $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $result = mysqli_query($conn,"update `Game` set `FirstJackIndex`={$ix}, `FirstJackPosition`='{$position}' where `ID` = '{$gameID}'");
    mysqli_close($conn);
    return $result;
  }
  
  function insertFDeal($gameID, $dealID) {
    global $errorMsg, $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $ID = GUID();
    
    $sql = "insert into `GameDeal` (`ID`, `DealID`, `GameID`,`DateInserted`) values ('{$ID}','{$dealID}','{$gameID}',now())";
    if (mysqli_query($conn, $sql) === false) {
      $errorMsg .= mysqli_error($conn);
    } else {
      $sql = "update `Game` set `FirstJackIndex` = 0, `FirstJackPosition`='L' where `ID`='{$gameID}'";
      if (mysqli_query($conn, $sql) === false) {
        $errorMsg .= mysqli_error($conn);
      }
    }

    mysqli_close($conn);
    return $result;
  }
  
  function getRandomFDeal() {
    global $errorMsg, $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $fdeal = array();
    $r = mt_rand(1,500);
    
    // for debugging:
    $r = 10;
    
    $sql = "select `ID`,`Cards` from `Deal` where `PurposeCode` = 'J' and `ID`={$r}";
      
    $results = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($results)) {
      $fdeal['DealID'] = $row['ID'];
      $fdeal['Cards'] = $row['Cards'];
    }

    mysqli_close($conn);
    return $fdeal;
  }
  
?>