<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName]) && isset($_POST['position'])) {
      
      $errorMsg = "";
      $gameID = $_POST['gameID'];
      $position = $_POST['position'];
      $isFirst = isset($_POST['isFirst']);
      $turn = getTurn($position);
      $sql = "";
      
      if ($isFirst) {
        $sql = "update `Game` set `FirstDealPosition`='{$position}',`Dealer`='{$position}', `Turn`='{$turn}', `CardFaceUp`='    ' where `ID`='{$gameID}'";
      } else {
        $sql = "update `Game` set `Dealer`='{$position}', `Turn`='{$turn}', `CardFaceUp`='    ' where `ID`='{$gameID}'";
      }
      
      $result = mysqli_query($connection, $sql);
      if ($result === false)
        $errorMsg = mysqli_error($connection);
      else 
        $errorMsg = 'OK';
      
      http_response_code(200);
      echo $errorMsg;
      
    } else {
      echo "ID or position invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

  function getTurn($position) {
    $turn = ' ';
    if ($position == 'O') {
      $turn = 'L';
    } else if ($position == 'L') {
      $turn = 'P';
    } else if ($position == 'P') {
      $turn = 'R';
    } else if ($position == 'R') {
      $turn = 'O';
    } 
    return $turn;
  }


?>