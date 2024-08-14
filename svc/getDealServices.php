<?php 
  // relative to phpEuchre\api
  include_once('../config/db.php');
  include_once('../config/config.php');


  function getDealID($conn, $gameID) {
    $response = array();
    $response['ErrorMsg'] = "";
    $response['DealID'] = "";
    
    $sql = "select `DealID` 
      from `GameDeal` gd
      join `Deal` d on gd.`DealID` = d.`ID`
      where gd.`GameID` = '{$gameID}' and d.`PurposeCode` = 'J'";
    
    $results = mysqli_query($conn, $sql);
    if ($results === false) {
      $response['ErrorMsg'] .= mysqli_error($conn);
    } else {
      while ($row = mysqli_fetch_array($results)) {
        $response['DealID'] = is_null($row['DealID']) ? '' : $row['DealID'];
      }
    }
    
    return $response;
  }
  
  function getCurrentFDeal($conn, $gameID) {
    $fdeal = array();
    $fdeal['ErrorMsg'] = "";
    
    $sql = "select `FirstJackIndex`,`FirstJackPosition`,d.`Cards`
      from `Game` g
      join `GameDeal` gd on g.ID = gd.`GameID`
      join `Deal` d on gd.`DealID` = d.`ID` and d.`PurposeCode` = 'J'
      where g.ID = '{$gameID}'";

    $results = mysqli_query($conn, $sql);
    if ($results === false) {
      $fdeal['ErrorMsg'] .= mysqli_error($conn);
    } else {
      while ($row = mysqli_fetch_array($results)) {
        $fdeal['Index'] = $row['FirstJackIndex'];
        $fdeal['Cards'] = $row['Cards'];
        $fdeal['Position'] = $row['FirstJackPosition'];
      }
    }
    
    return $fdeal;
  }

?>