<?php
  include_once('../../config/db.php');
  include_once('../../config/config.php');
  include('../../controllers/isAuthenticated.php');
  include('../../svc/services.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      $games = array();
      $d = cutoffDate();
      
      $sql = "  select 
        g.`ID` GameID
        ,ACP,ACR,ACL
        ,g.`GameStartDate`
        ,g.`PartnerJoinDate`
        ,g.`LeftJoinDate`
        ,g.`RightJoinDate`
        ,g.`PartnerJoinDate` is not null and g.`PartnerJoinDate` >= '{$d}' `PCanRejoin`
        ,g.`LeftJoinDate` is not null and g.`LeftJoinDate` >= '{$d}' `LCanRejoin`
        ,g.`RightJoinDate` is not null and g.`RightJoinDate` >= '{$d}' `RCanRejoin`
        ,g.`GameStartDate` is not null and g.`GameStartDate` >= '{$d}' `OCanRejoin`
        ,g.`GameFinishDate`
        ,g.`GameFinishDate` is not null `GameIsFinished`
        ,op.`Name` `OName`
        ,pp.`Name` `PName`
        ,lp.`Name` `LName`
        ,rp.`Name` `RName`
        ,'{$d}' `CutoffDate`
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
      order by g.`GameStartDate` desc
      limit 100
      ;";

      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $games['ErrorMsg'] = mysqli_error($connection);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $r = array();
          $r['GameID'] = $row['GameID'];
          $r['PCanRejoin'] = $row['PCanRejoin'];
          $r['OCanRejoin'] = $row['OCanRejoin'];
          $r['LCanRejoin'] = $row['LCanRejoin'];
          $r['RCanRejoin'] = $row['RCanRejoin'];
          $r['CutoffDate'] = $row['CutoffDate'];
          $r['GameFinishDate'] = $row['GameFinishDate'];
          $r['GameStartDate'] = $row['GameStartDate'];
          $r['OName'] = $row['OName'];
          $r['PName'] = $row['PName'];
          $r['LName'] = $row['LName'];
          $r['RName'] = $row['RName'];
          array_push($games, $r);
        }
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