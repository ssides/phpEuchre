<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getThumbnailURL.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $errorMsg = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST[$cookieName];
      $game = array();
      
      $sql = 
        "select 
          `Organizer`,`Partner`,`Left`,`Right`,`OrganizerScore`,`OpponentScore`,`GameStartDate`,`Dealer`,`Trump`,`OrganizerTricks`,`OpponentTricks`,`AJP`,`AJR`,`AJL`
          ,ou.`ThumbnailPath` `OThumbnailPath`,op.`Name` `OName`
          ,pu.`ThumbnailPath` `PThumbnailPath`,pp.`Name` `PName`
          ,lu.`ThumbnailPath` `LThumbnailPath`,lp.`Name` `LName`
          ,ru.`ThumbnailPath` `RThumbnailPath`,rp.`Name` `RName`
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
        $errorMsg = mysqli_error($connection);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $game['Organizer'] = $row['Organizer'];
          $game['Partner'] = $row['Partner'];
          $game['Left'] = $row['Left'];
          $game['Right'] = $row['Right'];
          $game['OrganizerScore'] = $row['OrganizerScore'];
          $game['OpponentScore'] = $row['OpponentScore'];
          $game['GameStartDate'] = $row['GameStartDate'];
          $game['Dealer'] = $row['Dealer'];
          $game['Trump'] = $row['Trump'];
          $game['OrganizerTricks'] = $row['OrganizerTricks'];
          $game['OpponentTricks'] = $row['OpponentTricks'];
          $game['OThumbnailURL'] = is_null($row['OThumbnailPath']) ? '' : getThumbnailURL($row['OThumbnailPath']);
          $game['OName'] = $row['OName'];
          $game['PThumbnailURL'] =  is_null($row['PThumbnailPath']) ? '' : getThumbnailURL($row['PThumbnailPath']);
          $game['PName'] = $row['PName'];
          $game['LThumbnailURL'] =  is_null($row['LThumbnailPath']) ? '' : getThumbnailURL($row['LThumbnailPath']);
          $game['LName'] = $row['LName'];
          $game['RThumbnailURL'] =  is_null($row['RThumbnailPath']) ? '' : getThumbnailURL($row['RThumbnailPath']);
          $game['RName'] = $row['RName'];
          $game['AJP'] = is_null($row['AJP']) ? '' : $row['AJP'];
          $game['AJR'] = is_null($row['AJR']) ? '' : $row['AJR'];
          $game['AJL'] = is_null($row['AJL']) ? '' : $row['AJL'];
        }
      }

      if (!isset($game['GameStartDate']) || is_null($game['GameStartDate'])) {
        $errorMsg .= "Missing Game Start Date for game: {$gameID}";
      }
      $game['ErrorMsg'] = $errorMsg;

      http_response_code(200);
      echo json_encode($game);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>