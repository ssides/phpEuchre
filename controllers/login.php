<?php
  include_once('config/db.php');
  include_once('config/config.php');
  include_once('controllers/isAuthenticated.php');
  include_once('svc/group.php');
  include('svc/cookie.php');
  
  if (isAppAuthenticated()) {
    header('Location: dashboard.php');
  } else if($_SERVER["REQUEST_METHOD"] === 'POST') {
      $name_signin      = $_POST['name_signin'];
      $password_signin  = $_POST['password_signin'];
      $group_signin  = $_POST['group_signin'];
      $group_id  = $_POST['group_id'];
      $group = array();
      $group['ID'] = "";
      $group['Description'] = "";
      $groupFail = false;
      $loginError = is_null($connection) ? "No connection. " : "";
      
      if(empty($name_signin)){
        $loginError .= "Name missing.";
      } else if (empty($password_signin)) {
        $loginError .= "Password missing.";
      } else {
        $login = verifyCredentials($name_signin, $password_signin);
        
        if (empty($login['ErrorMsg']) && isset($login['ID'])) {
          if (!empty($group_signin) && !empty($group_id)) {
            $group = verifyGroup($login, $group_id);
            if (!isset($group['ID'])) {
              $loginError = "You are not a member of that group.";
              $groupFail = true;
            }
          }
          
          if (!$groupFail) {
            if (setLoginCookieAndGroup($login['ID'], $group) === true) {
              header("Location: ./dashboard.php");
            } else {
              $loginError = "Could not log in.";
            }
          }
        } else {
          $loginError = $login['ErrorMsg'];
        }
      }
  }
  
  function verifyGroup($login, $group_id) {
    global $connection;
    $retVal = array();
    $retVal['ErrorMsg'] = '';
    $gid = mysqli_real_escape_string($connection, $group_id);

    $sql = "select pg.GroupID, g.`Description`
      from `PlayerGroup` pg 
      join `Group` g on pg.`GroupID` = g.`ID`
      where pg.`PlayerID` = '{$login['ID']}' and pg.`GroupID` = '{$gid}' and pg.`IsActive` = '1'";
      
    $results = mysqli_query($connection, $sql);
    if ($results === false) {
      $retVal['ErrorMsg'] .= mysqli_error($connection);
    } else {
      while($row = mysqli_fetch_array($results)) {
        $retVal['ID'] = $row['GroupID'];
        $retVal['Description'] = $row['Description'];
      }
    }

    return $retVal;
  }
  
  function verifyCredentials($name_signin, $password_signin) {
    global $connection;
    $retVal = array();
    $retVal['ErrorMsg'] = '';
    $userName = mysqli_real_escape_string($connection, $name_signin);
    $pswd = mysqli_real_escape_string($connection, $password_signin);

    $user = getUser($userName);
    if (empty($user['ErrorMsg'])) {
      if (isset($user['Password'])) {
        $password = password_verify($pswd, $user['Password']);
        if ($pswd == $password) {
          // credentials match.
          if ($user['IsActive'] == '1') {
            $retVal['ID'] = $user['ID'];
          } else {
            $retVal['ErrorMsg'] = 'User is disabled.';
          }
        } else {
          $retVal['ErrorMsg'] = 'Access denied.';
        }
      } else {
        $retVal['ErrorMsg'] .= "User account does not exist.";
      }
    } else {
      $retVal['ErrorMsg'] .= $user['ErrorMsg'];
    }
    
    return $retVal;
  }
  
  function getUser($userName) {
    global $connection;
    $retVal = array();
    $retVal['ErrorMsg'] = '';

    $sql = "select * from `Player` where `Name` = '{$userName}'";
    $results = mysqli_query($connection, $sql);
    if ($results === false) {
      $retVal['ErrorMsg'] .= mysqli_error($connection);
    } else {
      while($row = mysqli_fetch_array($results)) {
          $retVal['ID'] = $row['ID'];
          $retVal['Password'] = $row['Password'];
          $retVal['IsActive'] = $row['IsActive'];
      }
    }

    return $retVal;
  }

  function setLoginCookieAndGroup($pid, $group) {
    $result = false;
    if (function_exists('setcookie') === true)
    {
      $s = setLoginCookie([
        'r' => $pid,
        'k' => $group['ID'],
        'l' => $group['Description']
      ]);
      $result = true;
    }
    return $result;
  }

?>