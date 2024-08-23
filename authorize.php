<?php
  include_once('controllers/isAuthenticated.php');
  
  if (isAppAuthenticated() === false) {
    header('Location: index.php');
  }
?>