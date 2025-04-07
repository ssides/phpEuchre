<?php
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  include('../../svc/services.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['gameID'])) {
      $games = array();
      $games['ErrorMsg'] = "";

      $_SESSION['replayGameID'] = $_POST['gameID'];
      $_SESSION['replayFromActiveGame'] = 'false';
      
      http_response_code(200);
      echo json_encode($games);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
  
?>