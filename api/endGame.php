<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getNextTurn.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      
      $sql = "update `Game` set `DateFinished` = now(), `Dealer` = null, `Turn` = null, `CardFaceUp` = null where `ID`='{$gameID}'";
         
      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] = mysqli_error($connection);
      } 

      http_response_code(200);
      
      echo json_encode($response);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>