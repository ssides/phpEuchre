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
  
  // selects a deal at random. does not deal the same cards twice in the same game.
  function getRandomDeal($gameID) {
    global $hostname, $username, $password, $dbname, $dealChoices;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);  // todo use $connection.
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
      
    $results = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($results)) {
      $deal['DealID'] = $row['ID'];
      $deal['Cards'] = $row['Cards'];
    }

    mysqli_close($conn);
    return $deal;
  }

?>