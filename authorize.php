<?php
  include_once('config/db.php');
  include_once('config/config.php');

  function validUser($id) {
    if(empty($id)) {
      return false;
    }
    
    $nameCheckResult = mysqli_query($connection, "select * from `Players` where `PlayerID` = '{$id}' and `IsActive` = '1'");
    if (!$nameCheckResult) {
      return false;
    }
    if (mysqli_num_rows($nameCheckResult) == 0) {
      return false;
    }
    
    return true;
  }
  
  if (!validUser($_COOKIE[$cookieName]))
  {
    header('Location: index.php');
  }
?>