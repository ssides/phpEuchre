<?php 
  include_once('config/config.php');
  
  function removeCookie()
  {
    global $cookieName;
    if(isset($_COOKIE[$cookieName])) {
      $pid = $_COOKIE[$cookieName];
      unset($_COOKIE[$cookieName]);
      setcookie($cookieName, '', time() - 50000);
    }
  }
  
  function setLoginCookie($pid) {
    global $cookieName;
    $expirationDays = 3;
    return setcookie($cookieName, $pid, [
      'expires' => time() + ($expirationDays * 24 * 60 * 60),
      'samesite' => 'Lax',
    ]);
  }
?>