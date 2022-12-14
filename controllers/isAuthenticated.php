<?php 
  include_once('config/db.php');
  include_once('config/config.php');

  function isAuthenticated($id) {
    global $connection;
    $result = false;
    
    if(!empty($id)) {
      $nameCheckResult = mysqli_query($connection, "select * from `Player` where `ID` = '{$id}' and `IsActive` = '1'");
      if ($nameCheckResult !== false) {
        if (mysqli_num_rows($nameCheckResult) > 0) {
          $result = true;
        }
      }
    }
    
    return $result;
  }

?>