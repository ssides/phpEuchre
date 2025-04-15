<?php 
  // relative to phpEuchre/api
  include_once('../config/db.php');
  include_once('../config/config.php');

  function getDealID($conn, $gameID) {
    $response = [
        'DealID' => ''
    ];
    
    $sql = "SELECT `DealID` 
            FROM `GameDeal` gd
            JOIN `Deal` d ON gd.`DealID` = d.`ID`
            WHERE gd.`GameID` = ? AND d.`PurposeCode` = 'J'";
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }
    if (!mysqli_stmt_bind_param($stmt, 's', $gameID)) { throw new Exception(mysqli_stmt_error($stmt)); }
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
    
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) { throw new Exception(mysqli_stmt_error($stmt)); }
    
    // one row in result set
    while ($row = mysqli_fetch_assoc($result)) {
        $response['DealID'] = $row['DealID'] ?? '';
    }
    
    mysqli_stmt_close($stmt);
    
    return $response;
  }

  function getCurrentFDeal($conn, $gameID) {
    $fdeal = [
        'Index' => '',
        'Cards' => '',
        'Position' => ''
    ];
    
    $sql = "SELECT `FirstJackIndex`, `FirstJackPosition`, d.`Cards`
            FROM `Game` g
            JOIN `GameDeal` gd ON g.ID = gd.`GameID`
            JOIN `Deal` d ON gd.`DealID` = d.`ID` AND d.`PurposeCode` = 'J'
            WHERE g.ID = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) { throw new Exception(mysqli_error($conn)); }
    if (!mysqli_stmt_bind_param($stmt, 's', $gameID)) { throw new Exception(mysqli_stmt_error($stmt)); }
    if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }
    
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) { throw new Exception(mysqli_stmt_error($stmt)); }
    
    // one row in result set
    while ($row = mysqli_fetch_assoc($result)) {
        $fdeal['Index'] = $row['FirstJackIndex'] ?? '';
        $fdeal['Cards'] = $row['Cards'] ?? '';
        $fdeal['Position'] = $row['FirstJackPosition'] ?? '';
    }
    
    mysqli_stmt_close($stmt);
    
    return $fdeal;
  }

?>