<?php
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $response = array();
      $response['ErrorMsg'] = "";

      $games = array();
      $d = cutoffDate();
      $response['CutoffDate'] = $d;

      $sql = " select 
            g.`GameFinishDate`
            ,op.`Name` `OName`
            ,pp.`Name` `PName`
            ,g.`OrganizerScore`
            ,lp.`Name` `LName`
            ,rp.`Name` `RName`
            ,g.`OpponentScore`
          from `Game` g
          left join `UserProfile` ou on g.`Organizer` = ou.`PlayerID`
          join `Player` op on g.`Organizer` = op.`ID`
          left join `UserProfile` pu on g.`Partner` = pu.`PlayerID`
          join `Player` pp on g.`Partner` = pp.`ID`
          left join `UserProfile` lu on g.`Left` = lu.`PlayerID`
          join `Player` lp on g.`Left` = lp.`ID`
          left join `UserProfile` ru on g.`Right` = ru.`PlayerID`
          join `Player` rp on g.`Right` = rp.`ID`
          where g.`GameStartDate` is not null
          and g.`GameFinishDate` is not null
          and g.`GameFinishDate` > '{$d}'
          order by g.`GameFinishDate`
      ";

      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($connection);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $r = array();
          $r['GameFinishDate'] = $row['GameFinishDate'];
          $r['OName'] = $row['OName'];
          $r['PName'] = $row['PName'];
          $r['OrganizerScore'] = $row['OrganizerScore'];
          $r['LName'] = $row['LName'];
          $r['RName'] = $row['RName'];
          $r['OpponentScore'] = $row['OpponentScore'];
          array_push($games, $r);
        }
      }

      $response['Games'] = $games;
      http_response_code(200);
      echo json_encode($response);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
  
  function cutoffDate() {
    $now = new DateTime();
    $now->sub(new DateInterval('P6M'));
    return $now->format('Y-m-d');
  }

?>