<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php'); // for GUID
  include('../svc/getNextPosition.php');
  include('../svc/getDealServices.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $errorMsg = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST[$cookieName];
      $card = array();
      
      $dealID = getDealID($gameID);
      if (is_null($dealID)) {
        $d = getRandomFDeal();
        insertFDeal($gameID, $d['DealID']);
        $card['ID'] = substr($d['Cards'], 0, 2);
        $card['Position'] = 'L';
      } else {
        $c = getCurrentFDeal($gameID);
        if ($c['Cards'][$c['Index']] != 'J') {
          $c['Index'] += 3;
          $card['ID'] = substr($c['Cards'], $c['Index'], 2);
          $card['Position'] = getNextPosition($c['Position']);
          updateFDeal($gameID, $c['Index'], $card['Position']);
        } else {
          $card['ID'] = substr($c['Cards'], $c['Index'], 2);
          $card['Position'] = $c['Position'];
        }
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
    mysqli_query($conn, "START TRANSACTION;");
    $result = mysqli_query($conn,"update `Game` set `FirstJackIndex`={$ix}, `FirstJackPosition`='{$position}' where `ID` = '{$gameID}'");
    mysqli_query($conn, "COMMIT;");
    mysqli_close($conn);
    return $result;
  }
  
  function insertFDeal($gameID, $dealID) {
    global $errorMsg, $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $ID = GUID();
    
    $sql = "insert into `GameDeal` (`ID`, `DealID`, `GameID`,`InsertDate`) values ('{$ID}','{$dealID}','{$gameID}',now())";
    if (mysqli_query($conn, $sql) === false) {
      $errorMsg .= mysqli_error($conn);
    } else {
      $sql = "update `Game` set `FirstJackIndex` = 0, `FirstJackPosition`='L' where `ID`='{$gameID}'";
      
      mysqli_query($conn, "START TRANSACTION;");
      if (mysqli_query($conn, $sql) === false) {
        $errorMsg .= mysqli_error($conn);
        mysqli_query($conn, "ROLLBACK;");
      } else {
        mysqli_query($conn, "COMMIT;");
      }
    }

    mysqli_close($conn);
    return $result;
  }
  
  function getRandomFDeal() {
    global $errorMsg, $hostname, $username, $password, $dbname, $firstJackChoices;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $fdeal = array();
    $r = mt_rand(0, $firstJackChoices - 1);
    
    $sql = "
      select `ID`,`Cards` 
      from `Deal` 
      where `PurposeCode` = 'J' 
      order by `ID`
      limit 1 offset {$r}";
      
    $results = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($results)) {
      $fdeal['DealID'] = $row['ID'];
      $fdeal['Cards'] = $row['Cards'];
    }

    mysqli_close($conn);
    return $fdeal;
  }
  
?>