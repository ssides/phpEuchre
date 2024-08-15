<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getNextTurn.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName]) && isset($_POST['position'])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $position = $_POST['position'];
      $isFirst = isset($_POST['isFirst']);
      
      $turn = getNextTurn($position);
      $sql = "";
      
      if ($isFirst) {
        $sql = "update `Game` set `FirstDealPosition`='{$position}',`Dealer`='{$position}', `Turn`='{$turn}', `CardFaceUp`=null where `ID`='{$gameID}'";
      } else {
        $sql = "update `Game` set `Dealer`='{$position}', `Turn`='{$turn}', `CardFaceUp`=null where `ID`='{$gameID}'";
      }
      
      $conn = mysqli_connect($hostname, $username, $password, $dbname);
      
      mysqli_query($conn, "START TRANSACTION;");
      $result = mysqli_query($conn, $sql);
      if ($result === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
        mysqli_query($conn, "ROLLBACK;");
      } else {
        mysqli_query($conn, "COMMIT;");
      }
      
      mysqli_close($conn);

      http_response_code(200);
      
      echo json_encode($response);
      
    } else {
      echo "ID or position invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>