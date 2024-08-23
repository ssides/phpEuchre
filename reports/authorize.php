<?php
  // for reports pages.
  include_once('../config/db.php');
  include_once('../config/config.php');
  include_once('../controllers/isAuthenticated.php');
  
  if (isAppAuthenticated() === false) {
    header('Location: ../index.php');
  } 

?>