<?php
  include_once('../config/db.php');
  include_once('../config/config.php');
  include_once('../controllers/isAuthenticated.php');
  
  if (!isAuthenticated($_COOKIE[$cookieName])) {
    header('Location: ../index.php');
  } 

?>