<?php 
  
  function isAuthenticated($id) {
    global $connection;
    $result = false;
    
    if(!empty($id)) {
      $sql = "select `ID` from `Player` where `ID` = '{$id}' and `IsActive` = '1'";
      $results = mysqli_query($connection, $sql);
      if ($results !== false) {
        $result = mysqli_num_rows($results) > 0;
      }
    }
    
    return $result;
  }
  
  function readAuthCookie() {
    global $_COOKIE, $cookieName, $$a;
    $result = false;
    
    if (isset($$a['r']) && strlen($$a['r']) > 0) {
      $result = true;
    } else if (!empty($_COOKIE[$cookieName])) {
      $$a = unserialize(base64_decode($_COOKIE[$cookieName]));
      $result = true;
    }
    
    return $result;
  }
  
  function isAppAuthenticated() {
    global $$a;
    $result = false;
    
    if (isset($$a['r']) || readAuthCookie()) {
      $result = isAuthenticated($$a['r']);
    }
    
    return $result;
  }


?>