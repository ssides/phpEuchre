<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  // I as positionID am acknowledging that I have seen the card played by playerID.
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $positionID = $_POST['positionID'];
      $playerID = $_POST['playerID'];
      
      $ack = "";
      $conn = mysqli_connect($hostname, $username, $password, $dbname);
      mysqli_query($conn, "START TRANSACTION;");

      $sql = "select `AC{$positionID}` `ACK` from `Game` where `ID`='{$gameID}'";
      
      $results = mysqli_query($conn, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $ack = is_null($row['ACK']) ? '' : $row['ACK'];
        }
      }
      
      if (strpos($ack, $playerID) === false) {
        $ack .= $playerID;
        $sql = "update `Game` set `AC{$positionID}`='{$ack}' where `ID`='{$gameID}'";
        $result = mysqli_query($conn, $sql);
        if ($result === false) {
          $response['ErrorMsg'] .= mysqli_error($conn);
        }
      } else {
        $response['ErrorMsg'] .= "{$positionID} already acknowledged card played by {$playerID}.";
      }
      
      if (strlen($response['ErrorMsg']) > 0) {
        mysqli_query($conn, "ROLLBACK;");
      } else {
        mysqli_query($conn, "COMMIT;");
      }
      
      mysqli_close($conn);

      http_response_code(200);
      
      echo json_encode($response);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>