<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php');
  include('../svc/toEasternTime.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $playerID = $_POST[$cookieName];
      $games = array();
      $d = cutoffDate();

      $sql = "select 
          `ID` `GameID`
          ,`InsertDate`
          ,`OrganizerScore`
          ,`OpponentScore`
        from `Game` 
        where `Organizer` = '{$playerID}' 
          and `InsertDate` >= '{$d}'
          and `GameFinishDate` is null
          and `GameEndDate` is null";
      
      $results = mysqli_query($connection, $sql);
      
      while ($row = mysqli_fetch_array($results)) {
        $et = toEasternTime($row['InsertDate']);
        $fet = $et->format('m-d-Y h:i a')." et";
        array_push($games, array($row['GameID'],$fet,$row['OrganizerScore'],$row['OpponentScore']));
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