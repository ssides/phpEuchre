<?php 
  include_once('config/config.php');
  
  function removeCookie()
  {
    global $cookieName;
    if(isset($_COOKIE[$cookieName])) {
      unset($_COOKIE[$cookieName]);
    }

    return setcookie($cookieName, '', time() - 3600);
  }

?>