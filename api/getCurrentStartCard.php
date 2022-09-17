<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getDealServices.php');
  
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

?>