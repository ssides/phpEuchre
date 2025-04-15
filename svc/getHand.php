<?php
  // relative to phpEuchre\api
  include_once('../config/db.php');
  include_once('../config/config.php');

  // Get a dealt hand from `Play` for one player
  // throws exceptions on error.
  function getHand($conn, $gameID, $positionID) {
    $play = [];
    
    $sql = "SELECT `ID`,`CardID1`,`CardID2`,`CardID3`,`CardID4`,`CardID5` FROM `Play` WHERE `Position` = ? AND `GameID` = ? ORDER BY `InsertDate` DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { throw new Exception(mysqli_error($conn)); }
    
    mysqli_stmt_bind_param($stmt, "ss", $positionID, $gameID);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result === false) { throw new Exception("Invalid game state;"); }
        if ($row = mysqli_fetch_array($result)) {
            $play['PlayID'] = $row['ID'];
            $play['CardID1'] = $row['CardID1'];
            $play['CardID2'] = $row['CardID2'];
            $play['CardID3'] = $row['CardID3'];
            $play['CardID4'] = $row['CardID4'];
            $play['CardID5'] = $row['CardID5'];
        }
        mysqli_free_result($result);
    } else {
        throw new Exception(mysqli_error($conn)); 
    }
    
    mysqli_stmt_close($stmt);
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