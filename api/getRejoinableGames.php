<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $playerID = $_POST[$cookieName];
      $ary = array();
      $d = cutoffDate();
      
      $sql = "select
            g.`ID` `GameID`
            ,case
                when `Organizer` = '{$playerID}' then 'You'
                else org.Name
             end `OrganizerName`
            ,case
                when `Partner` = '{$playerID}' then 'Partner'
                when `Left` = '{$playerID}' then 'Opponent Left'
                when `Right` = '{$playerID}' then 'Opponent Right'
                when `Organizer` = '{$playerID}' then 'Organizer'
                else 'Unknown'
            end `Position`
          from `Game` g
          join `Player` org on g.Organizer = org.ID
          where ((`Partner` = '{$playerID}' and PartnerJoinDate is not null and PartnerJoinDate > '{$d}')
              or (`Left` = '{$playerID}' and LeftJoinDate is not null and LeftJoinDate > '{$d}')
              or (`Right` = '{$playerID}' and RightJoinDate is not null and RightJoinDate > '{$d}')
              or (`Organizer` = '{$playerID}' and GameStartDate is not null and GameStartDate > '{$d}'))
            and g.`DateFinished` is null
          order by g.`DateInserted` desc";
            
      $results = mysqli_query($connection, $sql);
      
      while ($row = mysqli_fetch_array($results)) {
        array_push($ary, array($row['GameID'],$row['OrganizerName'],$row['Position']));
      }

      http_response_code(200);
      echo json_encode($ary);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
  
  function cutoffDate() {
    $now = new DateTime(date("Y-m-d"));
    $now->sub(new DateInterval('P3D'));
    return $now->format('Y-m-d');
  }
  
?>