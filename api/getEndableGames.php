<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $playerID = $_POST[$cookieName];
      $games = array();
      $d = cutoffDate();

      $sql = "select 
          `ID` `GameID`
          ,DATE_FORMAT(`InsertDate`, '%m-%d-%Y %h:%i et') `InsertDate` 
          ,OrganizerScore
          ,OpponentScore
        from `Game` 
        where `Organizer` = '{$playerID}' 
          and `InsertDate` >= '{$d}'
          and `GameFinishDate` is null
          and `GameEndDate` is null";
      
      $results = mysqli_query($connection, $sql);
      
      while ($row = mysqli_fetch_array($results)) {
        array_push($games, array($row['GameID'],$row['InsertDate'],$row['OrganizerScore'],$row['OpponentScore']));
      }

      http_response_code(200);
      echo json_encode($games);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
  
?>