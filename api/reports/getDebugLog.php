<?php
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      $games = array();
      $log = array();
      $gameID = $_POST['gameID'];

      $sql = "select `DealID`,`GameControllerState`,`InsertDate`,`Message`,`OpponentScore`,`OpponentTricks`,`OrganizerScore`,`OrganizerTricks`,`PositionID`,`Dealer`,`Turn`,`CardFaceUp`,`ACO`,`ACP`,`ACL`,`ACR`,`PO`,`PP`,`PL`,`PR`
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
          $r['Dealer'] = is_null($row['Dealer']) ? '' : $row['Dealer'];
          $r['Turn'] = is_null($row['Turn']) ? '' : $row['Turn'];
          $r['CardFaceUp'] = is_null($row['CardFaceUp']) ? '' : $row['CardFaceUp'];
          $r['ACO'] = is_null($row['ACO']) ? '' : $row['ACO'];
          $r['ACP'] = is_null($row['ACP']) ? '' : $row['ACP'];
          $r['ACL'] = is_null($row['ACL']) ? '' : $row['ACL'];
          $r['ACR'] = is_null($row['ACR']) ? '' : $row['ACR'];
          $r['PO'] = is_null($row['PO']) ? '' : $row['PO'];
          $r['PP'] = is_null($row['PP']) ? '' : $row['PP'];
          $r['PL'] = is_null($row['PL']) ? '' : $row['PL'];
          $r['PR'] = is_null($row['PR']) ? '' : $row['PR'];
          
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