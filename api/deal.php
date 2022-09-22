<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $errorMsg = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST[$cookieName];
      $deal = array();
      
      $deal= getRandomDeal();
      while (!isset($deal['DealID'])) {
        $deal = getRandomDeal();
      }
      
      distributeCards($deal);
      
      $deal['ErrorMsg'] = $errorMsg;
      http_response_code(200);
      
      echo json_encode($deal);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

  function distributeCards($deal) {
    global $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    
    
  }
  
  // selects a deal at random. does not deal the same cards twice.
  function getRandomDeal() {
    global $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $deal = array();
    $r = mt_rand(1000,1999);  // todo: make the 1999 configurable.
    
    $sql = "
      select `ID`,`Cards` 
      from `Deal` d
      left join `GameDeal` gd on d.`ID` = gd.`DealID`
      where `PurposeCode` = 'D' 
      and gd.`DealID` is null
      and d.`ID`={$r}";
      
    $results = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($results)) {
      $deal['DealID'] = $row['ID'];
      $deal['Cards'] = $row['Cards'];
    }

    mysqli_close($conn);
    return $deal;
  }

?>