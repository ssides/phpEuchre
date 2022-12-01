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
      $positionID = $_POST['positionID'];
      $playerID = $_POST['playerID'];
      
      $ack = "";
      
      $sql = "select `AC{$positionID}` `ACK` from `Game` where `ID`='{$gameID}'";
      
      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($connection);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $ack = is_null($row['ACK']) ? '' : $row['ACK'];
        }
      }
      
      if (strlen($ack) < 3) {
        $ack .= $playerID;
        $sql = "update `Game` set `AC{$positionID}`='{$ack}' where `ID`='{$gameID}'";
        $result = mysqli_query($connection, $sql);
        if ($result === false) {
          $response['ErrorMsg'] .= mysqli_error($connection);
        }
      } else {
        $response['ErrorMsg'] .= "Too many acknowledgments from {$positionID} for {$playerID}.";
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