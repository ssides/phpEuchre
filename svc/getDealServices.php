<?php 
  // relative to phpEuchre\api
  include_once('../config/db.php');
  include_once('../config/config.php');


  function getDealID($gameID) {
    global $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $dealID = null;
    
    $sql = "select `DealID` 
      from `GameDeal` gd
      join `Deal` d on gd.`DealID` = d.`ID`
      where gd.`GameID` = '{$gameID}' and d.`PurposeCode` = 'J'";
    
    $results = mysqli_query($conn, $sql);
    
    while ($row = mysqli_fetch_array($results)) {
      $dealID = $row['DealID'];
    }

    mysqli_close($conn);
    return $dealID;
  }
  
  function getCurrentFDeal($gameID) {
    global $errorMsg, $hostname, $username, $password, $dbname;
    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    $fdeal = array();
    $sql = "select `FirstJackIndex`,`FirstJackPosition`,d.`Cards`
      from `Game` g
      join `GameDeal` gd on g.ID = gd.`GameID`
      join `Deal` d on gd.`DealID` = d.`ID` and d.`PurposeCode` = 'J'
      where g.ID = '{$gameID}'";

    $results = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($results)) {
      $fdeal['Index'] = $row['FirstJackIndex'];
      $fdeal['Cards'] = $row['Cards'];
      $fdeal['Position'] = $row['FirstJackPosition'];
    }
    
    mysqli_close($conn);
    return $fdeal;
  }

?>