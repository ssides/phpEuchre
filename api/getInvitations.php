<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $playerID = $_POST['r'];
      $invitations = array();
      $d = cutoffDate();

      $sql = "select
            g.`ID` `GameID`
            ,org.Name `OrganizerName`
            ,case
                when `Partner` = '{$playerID}' then 'Partner'
                when `Left` = '{$playerID}' then 'Opponent Left'
                when `Right` = '{$playerID}' then 'Opponent Right'
                else 'Unknown'
            end `Position`
          from `Game` g
          join `Player` org on g.Organizer = org.ID
          where ((`Partner` = '{$playerID}' and PartnerJoinDate is null)
              or (`Left` = '{$playerID}' and LeftJoinDate is null)
              or (`Right` = '{$playerID}' and RightJoinDate is null))
            and g.`InsertDate` >= '{$d}'
            and g.`GameEndDate` is null
          order by g.`InsertDate` desc";

      $results = mysqli_query($connection, $sql);
      
      while ($row = mysqli_fetch_array($results)) {
        array_push($invitations, array($row['GameID'],$row['OrganizerName'],$row['Position']));
      }

      http_response_code(200);
      echo json_encode($invitations);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
  
?>