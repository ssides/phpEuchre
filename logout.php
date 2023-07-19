<?php
  include('svc/cookie.php');
  session_start();
  session_destroy();
  
  removeCookie();
  header("Location: index.php");
?>