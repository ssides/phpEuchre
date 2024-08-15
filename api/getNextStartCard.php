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
      
      $conn =  mysqli_connect($hostname, $username, $password, $dbname);
      
      mysqli_query($conn, "START TRANSACTION;");
      
      $f = getDealID($conn, $gameID);
      $errorMsg .= $f['ErrorMsg'];
      if (strlen($f['DealID']) == 0) {
        $d = getRandomFDeal($conn);
        $errorMsg .= $d['ErrorMsg'];
        $errorMsg .= insertFDeal($conn, $gameID, $d['DealID']);
        $card['ID'] = substr($d['Cards'], 0, 2);
        $card['Position'] = 'L';
      } else {
        $c = getCurrentFDeal($conn, $gameID);
        $errorMsg .= $c['ErrorMsg'];
        if ($c['Cards'][$c['Index']] != 'J') {
          $c['Index'] += 3;
          $card['ID'] = substr($c['Cards'], $c['Index'], 2);
          $card['Position'] = getNextPosition($c['Position']);
          $errorMsg .= updateFDeal($conn, $gameID, $c['Index'], $card['Position']);
        } else {
          $card['ID'] = substr($c['Cards'], $c['Index'], 2);
          $card['Position'] = $c['Position'];
        }
      }
      
      $card['ErrorMsg'] = $errorMsg;
      
      if (strlen($errorMsg) > 0) {
        mysqli_query($conn, "ROLLBACK;");
      } else {
        mysqli_query($conn, "COMMIT;");
      }

      mysqli_close($conn);

      http_response_code(200);
      
      echo json_encode($card);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

  function updateFDeal($conn, $gameID, $ix, $position) {
    $errorMsg = "";
    $sql = "update `Game` set `FirstJackIndex`={$ix}, `FirstJackPosition`='{$position}' where `ID` = '{$gameID}'";
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
      $errorMsg .= mysqli_error($conn);
    }
    return $errorMsg;
  }
  
  function insertFDeal($conn, $gameID, $dealID) {
    $errorMsg = "";
    $ID = GUID();
    
    $sql = "insert into `GameDeal` (`ID`, `DealID`, `GameID`,`InsertDate`) values ('{$ID}','{$dealID}','{$gameID}',now())";
    if (mysqli_query($conn, $sql) === false) {
      $errorMsg .= mysqli_error($conn);
    } else {
      $sql = "update `Game` set `FirstJackIndex` = 0, `FirstJackPosition`='L' where `ID`='{$gameID}'";
      
      if (mysqli_query($conn, $sql) === false) {
        $errorMsg .= mysqli_error($conn);
      }
    }

    return $errorMsg;
  }
  
  function getRandomFDeal($conn) {
    global $firstJackChoices;
    $fdeal = array();
    $fdeal['ErrorMsg'] = "";
    $r = mt_rand(0, $firstJackChoices - 1);
    
    $sql = "
      select `ID`,`Cards` 
      from `Deal` 
      where `PurposeCode` = 'J' 
      order by `ID`
      limit 1 offset {$r}";
      
    $results = mysqli_query($conn, $sql);
    if ($results === false) {
      $fdeal['ErrorMsg'] .= mysqli_error($conn);
    } else {
      while ($row = mysqli_fetch_array($results)) {
        $fdeal['DealID'] = $row['ID'];
        $fdeal['Cards'] = $row['Cards'];
      }
    }

    return $fdeal;
  }
  
?>