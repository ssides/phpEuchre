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
      
      $sql = "select 
        `OrganizerScore`
        ,`OpponentScore`
        ,op.`Name` `OName`
        ,pp.`Name` `PName`
        ,lp.`Name` `LName`
        ,rp.`Name` `RName`
       from `Game` g
       left join `UserProfile` ou on g.`Organizer` = ou.`PlayerID`
       join `Player` op on g.`Organizer` = op.`ID`
       left join `UserProfile` pu on g.`Partner` = pu.`PlayerID`
       join `Player` pp on g.`Partner` = pp.`ID`
       left join `UserProfile` lu on g.`Left` = lu.`PlayerID`
       join `Player` lp on g.`Left` = lp.`ID`
       left join `UserProfile` ru on g.`Right` = ru.`PlayerID`
       join `Player` rp on g.`Right` = rp.`ID` 
       where g.`ID`='{$gameID}'";
         
      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] = mysqli_error($connection);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $response['OrganizerScore'] = $row['OrganizerScore'];
          $response['OpponentScore'] = $row['OpponentScore'];
          $response['OName'] = $row['OName'];
          $response['PName'] = $row['PName'];
          $response['LName'] = $row['LName'];
          $response['RName'] = $row['RName'];
        }
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