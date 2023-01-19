<?php
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      $games = array();
      $log = array();
      $gameID = $_POST['gameID'];

      $sql = "select `DealID`,`GameControllerState`,`InsertDate`,`Message`,`OpponentScore`,`OpponentTricks`,`OrganizerScore`,`OrganizerTricks`,`PositionID` 
        from `GameControllerLog` 
        where `GameID`='{$gameID}' order by `InsertDate`";

      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $games['ErrorMsg'] = mysqli_error($connection);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $r = array();
          $r['OrganizerScore'] = $row['OrganizerScore'];
          $r['OpponentScore'] = $row['OpponentScore'];
          $r['OrganizerTricks'] = $row['OrganizerTricks'];
          $r['OpponentTricks'] = $row['OpponentTricks'];
          $r['DealID'] = is_null($row['DealID']) ? '' : $row['DealID'];
          $r['GameControllerState'] = $row['GameControllerState'];
          $r['InsertDate'] = $row['InsertDate'];
          $r['Message'] = is_null($row['Message']) ? '' : $row['Message'];
          $r['PositionID'] = $row['PositionID'];
          
          array_push($log, $r);
        }
      }

      $games['Log'] = $log;
      
      http_response_code(200);
      echo json_encode($games);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
  
?>