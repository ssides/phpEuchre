<?php 
  include_once('config/config.php');
  
  function removeCookie()
  {
    global $cookieName;
    if(isset($_COOKIE[$cookieName])) {
      unset($_COOKIE[$cookieName]);
    }

    if (setcookie($cookieName, '', -1, '/')) {
      return true;
    } else {
      return false;
    }
  }

?>