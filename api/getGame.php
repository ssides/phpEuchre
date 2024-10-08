<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getThumbnailURL.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      $playerID = $_POST['r'];
      $game = array();
      
      $conn = mysqli_connect($hostname, $username, $password, $dbname);

      $sql = "select 
        `Organizer`
        ,`Partner`
        ,`Left`
        ,`Right`
        ,`Dealer`
        ,`Turn`
        ,`Lead`
        ,`CardFaceUp`
        ,`OrganizerTrump`
        ,`OrganizerTricks`
        ,`OrganizerScore`
        ,`OpponentTrump`
        ,`OpponentTricks`
        ,`OpponentScore`
        ,`PlayTo`
        ,`ACO`
        ,`ACP`
        ,`ACR`
        ,`ACL`
        ,`PO`
        ,`PP`
        ,`PL`
        ,`PR`
        ,`ScoringInProgress`
        ,`Speed`
        ,ou.`ThumbnailPath` `OThumbnailPath`,substr(op.`Name`,1,8) `OName`
        ,pu.`ThumbnailPath` `PThumbnailPath`,substr(pp.`Name`,1,8) `PName`
        ,lu.`ThumbnailPath` `LThumbnailPath`,substr(lp.`Name`,1,8) `LName`
        ,ru.`ThumbnailPath` `RThumbnailPath`,substr(rp.`Name`,1,8) `RName`
        ,`GameStartDate`
        ,`GameFinishDate`
        ,`GameEndDate`
        ,gd.`DealID`
      from `Game` g
      left join (select `DealID`,`GameID` from `GameDeal` where `GameID` = '{$gameID}' and `IsActive` = '1') gd on g.`ID` = gd.`GameID`
      left join `UserProfile` ou on g.`Organizer` = ou.`PlayerID`
      join `Player` op on g.`Organizer` = op.`ID`
      left join `UserProfile` pu on g.`Partner` = pu.`PlayerID`
      join `Player` pp on g.`Partner` = pp.`ID`
      left join `UserProfile` lu on g.`Left` = lu.`PlayerID`
      join `Player` lp on g.`Left` = lp.`ID`
      left join `UserProfile` ru on g.`Right` = ru.`PlayerID`
      join `Player` rp on g.`Right` = rp.`ID`
      where g.`ID`='{$gameID}'";
         
      $results = mysqli_query($conn, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $game['Organizer'] = $row['Organizer'];
          $game['Partner'] = $row['Partner'];
          $game['Left'] = $row['Left'];
          $game['Right'] = $row['Right'];
          $game['Dealer'] = $row['Dealer'];
          $game['Turn'] = is_null($row['Turn']) ? '' : $row['Turn'];
          $game['Lead'] = is_null($row['Lead']) ? '' : $row['Lead'];
          $game['CardFaceUp'] = is_null($row['CardFaceUp']) ? '' : $row['CardFaceUp'];
          $game['OrganizerTrump']  = is_null($row['OrganizerTrump']) ? '' : $row['OrganizerTrump'];
          $game['OrganizerTricks'] = is_null($row['OrganizerTricks']) ? '' : $row['OrganizerTricks'];
          $game['OrganizerScore'] = $row['OrganizerScore'];
          $game['OpponentTrump']   = is_null($row['OpponentTrump']) ? '' : $row['OpponentTrump'];
          $game['OpponentTricks']  = is_null($row['OpponentTricks']) ? '' : $row['OpponentTricks'];
          $game['OpponentScore'] = $row['OpponentScore'];
          $game['PlayTo'] = $row['PlayTo'];
          
          // ACP, ACR, and ACL are set to 'A' when the associated player acknowledges (automatically) seeing the first Jack.  
          // This is how game state is determined.  These columns are also used during play to make sure all players
          // have seen the card played.
          $game['ACO'] = is_null($row['ACO']) ? '' : $row['ACO'];
          $game['ACP'] = is_null($row['ACP']) ? '' : $row['ACP'];
          $game['ACR'] = is_null($row['ACR']) ? '' : $row['ACR'];
          $game['ACL'] = is_null($row['ACL']) ? '' : $row['ACL'];
          
          // PO, PP, PL, and PR are the cards each player has played in turn.
          $game['PO'] = is_null($row['PO']) ? '' : $row['PO'];
          $game['PP'] = is_null($row['PP']) ? '' : $row['PP'];
          $game['PL'] = is_null($row['PL']) ? '' : $row['PL'];
          $game['PR'] = is_null($row['PR']) ? '' : $row['PR'];
          $game['ScoringInProgress'] = $row['ScoringInProgress'];
          $game['Speed'] = $row['Speed'];
          $game['OThumbnailPath'] = $row['OThumbnailPath'];
          $game['OThumbnailURL'] = is_null($row['OThumbnailPath']) ? '' : getThumbnailURL($row['OThumbnailPath']);
          $game['OName'] = $row['OName'];
          $game['PThumbnailURL'] =  is_null($row['PThumbnailPath']) ? '' : getThumbnailURL($row['PThumbnailPath']);
          $game['PName'] = $row['PName'];
          $game['LThumbnailURL'] =  is_null($row['LThumbnailPath']) ? '' : getThumbnailURL($row['LThumbnailPath']);
          $game['LName'] = $row['LName'];
          $game['RThumbnailURL'] =  is_null($row['RThumbnailPath']) ? '' : getThumbnailURL($row['RThumbnailPath']);
          $game['RName'] = $row['RName'];
          $game['GameStartDate'] = $row['GameStartDate'];
          $game['GameFinishDate'] = is_null($row['GameFinishDate']) ? '' : $row['GameFinishDate'];
          $game['GameEndDate'] = is_null($row['GameEndDate']) ? '' : $row['GameEndDate'];
          $game['DealID'] = is_null($row['DealID']) ? '' : $row['DealID'];
        }
      }
      
      $response['Game'] = $game;

      mysqli_close($conn);

      http_response_code(200);
      
      echo json_encode($response);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>