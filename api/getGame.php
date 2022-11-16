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
          `Organizer`
          ,`Partner`
          ,`Left`
          ,`Right`
          ,`OrganizerTrump`
          ,`OrganizerTricks`
          ,`OrganizerScore`
          ,`OpponentTrump`
          ,`OpponentTricks`
          ,`OpponentScore`
          ,`GameStartDate`
          ,`Dealer`
          ,`Turn`
          ,`AJP`
          ,`AJR`
          ,`AJL`
          ,ou.`ThumbnailPath` `OThumbnailPath`,substr(op.`Name`,1,8) `OName`
          ,pu.`ThumbnailPath` `PThumbnailPath`,substr(pp.`Name`,1,8) `PName`
          ,lu.`ThumbnailPath` `LThumbnailPath`,substr(lp.`Name`,1,8) `LName`
          ,ru.`ThumbnailPath` `RThumbnailPath`,substr(rp.`Name`,1,8) `RName`
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
          $game['OrganizerTrump']  = is_null($row['OrganizerTrump']) ? '' : $row['OrganizerTrump'];
          $game['OrganizerTricks'] = is_null($row['OrganizerTricks']) ? '' : $row['OrganizerTricks'];
          $game['OrganizerScore'] = $row['OrganizerScore'];
          $game['OpponentTrump']   = is_null($row['OpponentTrump']) ? '' : $row['OpponentTrump'];
          $game['OpponentTricks']  = is_null($row['OpponentTricks']) ? '' : $row['OpponentTricks'];
          $game['OpponentScore'] = $row['OpponentScore'];
          $game['GameStartDate'] = $row['GameStartDate'];
          $game['Dealer'] = $row['Dealer'];
          $game['Turn'] = is_null($row['Turn']) ? '' : $row['Turn'];
          $game['OThumbnailPath'] = $row['OThumbnailPath'];
          $game['OThumbnailURL'] = is_null($row['OThumbnailPath']) ? '' : getThumbnailURL($row['OThumbnailPath']);
          $game['OName'] = $row['OName'];
          $game['PThumbnailURL'] =  is_null($row['PThumbnailPath']) ? '' : getThumbnailURL($row['PThumbnailPath']);
          $game['PName'] = $row['PName'];
          $game['LThumbnailURL'] =  is_null($row['LThumbnailPath']) ? '' : getThumbnailURL($row['LThumbnailPath']);
          $game['LName'] = $row['LName'];
          $game['RThumbnailURL'] =  is_null($row['RThumbnailPath']) ? '' : getThumbnailURL($row['RThumbnailPath']);
          $game['RName'] = $row['RName'];
          // AJP, AJR, and AJR are set when the associated player acknowledges (automatically) seeing the first Jack.  This is how game state is determined.
          $game['AJP'] = is_null($row['AJP']) ? '' : $row['AJP'];
          $game['AJR'] = is_null($row['AJR']) ? '' : $row['AJR'];
          $game['AJR'] = is_null($row['AJL']) ? '' : $row['AJL'];
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