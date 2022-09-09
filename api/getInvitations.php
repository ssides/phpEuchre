<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $playerID = $_POST[$cookieName];
      $invitations = array();
      $sql = "select
            g.`ID` `GameID`
            ,org.Name `OrganizerName`
            ,case
                when `Partner` = '{$playerID}' then 'Partner'
                when `Left` = '{$playerID}' then 'Opponent'
                when `Right` = '{$playerID}' then 'Opponent'
                else 'Unknown'
            end `Position`
          from `Game` g
          join `Player` org on g.Organizer = org.ID
          where (`Partner` = '{$playerID}' and PartnerJoinDate is null)
            or (`Left` = '{$playerID}' and LeftJoinDate is null)
            or (`Right` = '{$playerID}' and LeftJoinDate is null)";
      $results = mysqli_query($connection, $sql);
      
      while ($row = mysqli_fetch_array($results)) {
        array_push($invitations, array($row['GameID'],$row['OrganizerName'],$row['Position']));
      }

      http_response_code(200);
      echo json_encode($game);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
  
?>