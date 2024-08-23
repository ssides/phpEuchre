<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $positionID = $_POST['positionID'];
      $cards = array();
      
      $conn = mysqli_connect($hostname, $username, $password, $dbname);

      $sql = "
        select `CardID1`,`CardID2`,`CardID3`,`CardID4`,`CardID5` 
        from `Play` 
        where `GameID` = '{$gameID}' and `Position` = '{$positionID}'
        order by `InsertDate` desc
        limit 1;";
      
      $results = mysqli_query($conn, $sql);
      
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $cards['CardID1'] = $row['CardID1'];
          $cards['CardID2'] = $row['CardID2'];
          $cards['CardID3'] = $row['CardID3'];
          $cards['CardID4'] = $row['CardID4'];
          $cards['CardID5'] = $row['CardID5'];
        }
      }
      
      $response['Cards'] = $cards;
      if (!isset($cards['CardID1'])) {
        $response['ErrorMsg'] .= "No cards for '{$gameID}' position '{$positionID}'";
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