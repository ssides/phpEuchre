<?php
  include_once('config/db.php');
  include_once('config/config.php');

  function validUser($id) {
    
  }
  
  if (function_exists('getcookie') !== true
     || !validUser($_COOKIE[$cookieName]))
  {
    header('Location: index.php');
  }
?>