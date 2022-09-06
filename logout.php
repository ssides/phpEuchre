<?php
  include('svc/removeCookie.php');
  session_start();
  session_destroy();
  
  removeCookie();
  header("Location: index.php")
;?>