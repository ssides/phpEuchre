<?php 
  include_once('config/db.php');
  include_once('config/config.php');

  function isAuthenticated($id) {
    global $connection;
    $result = false;
    
    if(!empty($id)) {
      
      $smt = mysqli_prepare($connection, "select `ID` from `Player` where `ID` = ? and `IsActive` = '1'");
      mysqli_stmt_bind_param($smt, 's', $id);
      if (mysqli_stmt_execute($smt) !== false) {
        mysqli_stmt_store_result($smt);
        if (mysqli_stmt_num_rows($smt) > 0) {
          $result = true;
        }
      }
    }
    
    return $result;
  }

?>