<?php
  // relative to phpEuchre\api
  include_once('../config/db.php');
  include_once('../config/config.php');

  function getHand($gameID, $positionID) {
    global $connection;

    $play = array();
    $play['ErrorMsg'] = "";
    
    $sql = "select `ID`,`CardID1`,`CardID2`,`CardID3`,`CardID4`,`CardID5` from `Play` where `Position` = '{$positionID}' and `GameID` = '{$gameID}' order by `InsertDate` desc limit 1";
    $results = mysqli_query($connection, $sql);
    if ($results === false) {
      $play['ErrorMsg'] .= mysqli_error($connection);
    } else {
      while ($row = mysqli_fetch_array($results)) {
        $play['PlayID'] = $row['ID'];
        $play['CardID1'] = $row['CardID1'];
        $play['CardID2'] = $row['CardID2'];
        $play['CardID3'] = $row['CardID3'];
        $play['CardID4'] = $row['CardID4'];
        $play['CardID5'] = $row['CardID5'];
      }
    }

    return $play;
  }
  
  function getCardNumber($hand, $cardID) {
    $cardNumber = '0';
    
    if ($hand['CardID1'] == $cardID)
      $cardNumber = '1';
    else if ($hand['CardID2'] == $cardID)
      $cardNumber = '2';
    else if ($hand['CardID3'] == $cardID)
      $cardNumber = '3';
    else if ($hand['CardID4'] == $cardID)
      $cardNumber = '4';
    else if ($hand['CardID5'] == $cardID)
      $cardNumber = '5';
    
    return $cardNumber;
  }

?>