<?php 
  
  include_once('config/config.php');
  
  protected function removeCookie()
  {
    global $cookieName;
    if(isset($_COOKIE[$cookieName])) {
      unset($_COOKIE[$cookieName]);
    }

    return setcookie($cookieName, '', -1, '/');
  }

?>