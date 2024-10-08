<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getDealServices.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $errorMsg = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST['r'];
      $card = array();
      
      $conn =  mysqli_connect($hostname, $username, $password, $dbname);
      
      mysqli_query($conn, "START TRANSACTION;");

      $d = getDealID($conn, $gameID);
      $errorMsg .= $d['ErrorMsg'];
      if (strlen($d['DealID']) > 0) {
        $c = getCurrentFDeal($conn, $gameID);
        $errorMsg .= $c['ErrorMsg'];
        $card['ID'] = substr($c['Cards'], $c['Index'], 2);
        $card['Position'] = $c['Position'];
      } else {
        $card['ID'] = '';
        $card['Position'] = '';
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

?>