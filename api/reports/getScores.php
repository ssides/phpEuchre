<?php
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      $games = array();
      $scores = array();
      $gameID = $_POST['gameID'];

      $sql = "select `OpponentTrump`,`OrganizerTrump`,`Lead`,`CardO`,`CardL`,`CardP`,`CardR`,`OpponentScore`,`OrganizerScore`,`OpponentTricks`,`OrganizerTricks`,`CardFaceUp`,`Dealer`,`DealID`
        from `GamePlay` 
        where `GameID`='{$gameID}' order by `InsertDate`";

      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $games['ErrorMsg'] = mysqli_error($connection);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $r = array();
          $r['OrganizerTrump'] = $row['OrganizerTrump'];
          $r['OpponentTrump'] = $row['OpponentTrump'];
          $r['Lead'] = $row['Lead'];
          $r['CardO'] = $row['CardO'];
          $r['CardL'] = $row['CardL'];
          $r['CardP'] = $row['CardP'];
          $r['CardR'] = $row['CardR'];
          $r['OrganizerScore'] = $row['OrganizerScore'];
          $r['OpponentScore'] = $row['OpponentScore'];
          $r['OrganizerTricks'] = $row['OrganizerTricks'];
          $r['OpponentTricks'] = $row['OpponentTricks'];
          $r['CardFaceUp'] = $row['CardFaceUp'];
          $r['DealID'] = $row['DealID'];
          $r['Dealer'] = $row['Dealer'];
          array_push($scores, $r);
        }
      }

      $games['Scores'] = $scores;
      
      http_response_code(200);
      echo json_encode($games);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
  
?>