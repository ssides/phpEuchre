<?php
  include('controllers/isAuthenticated.php');
  
  if (isAuthenticated($_COOKIE[$cookieName]) === false)
  {
    header('Location: index.php');
  }
?>