<?php
include_once('controllers/isAuthenticated.php');
include('svc/cookie.php');

unset($_SESSION['register_error']);

if (isAppAuthenticated()) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    try {
        unset($_SESSION['login_error']);
        $loginError = '';

        $name_signin = trim($_POST['name_signin'] ?? '');
        $group_signin = $_POST['group_signin'] ?? '';
        $group_id = $_POST['group_id'] ?? '';


        if (empty($name_signin)) {
            $loginError = "Name missing.";
        } else {
            $login = verifyCredentials($name_signin);

            if (isset($login['ID'])) {
                $group = [];
                $groupFail = false;

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
                        exit;                    // ← Important
                    } else {
                        $loginError = "Could not log in.";
                    }
                }
            } else {
                $loginError = $login['ErrorMsg'] ?? "Login failed.";
            }
        }

        // If we reach here, there was an error
        if (!empty($loginError)) {
            // Show error on the login form (you'll need to handle this in HTML)
            $_SESSION['login_error'] = $loginError;
            header("Location: index.php");
            exit;
        }

    } catch (RuntimeException $e) {
        // Expected business errors (user disabled, not exist, etc.)
        $_SESSION['login_error'] = $e->getMessage();
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        // Unexpected errors
        trigger_error($e->getMessage() . "\n" . $e->getTraceAsString(), E_USER_ERROR);
        $_SESSION['login_error'] = "An unexpected error occurred. Please try again.";
        header("Location: index.php");
        exit;
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
      where pg.`PlayerID` = ? and pg.`GroupID` = ? and pg.`IsActive` = '1'";
    
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $login['ID'], $gid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $groupid, $description);
    
    if (mysqli_stmt_fetch($stmt)) {
      $retVal = [
        'ID'    => $groupid,
        'Description'  => $description
      ];
    } else {
      // user is not a member of that group.  $retVal['ID'] is not set.
    }

    mysqli_stmt_close($stmt);

    return $retVal;
  }
  
  function verifyCredentials($name_signin) {
    global $connection;
    $retVal = array();
    $userName = mysqli_real_escape_string($connection, $name_signin);

    $user = getUser($userName);
    if (isset($user['ID'])) {
      if ($user['IsActive'] == '1') {
        $retVal['ID'] = $user['ID'];
      } else {
        throw new RuntimeException("User is disabled.");
      }
    } else {
      throw new RuntimeException("User account does not exist.");
    }
    
    return $retVal;
  }
  
  function getUser($userName) {
    global $connection;
    $retVal = array();

    $sql = "select `ID`,`Name`,`IsActive`   from `Player` where `Name` = ?";

    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 's', $userName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $name, $isactive);

    if (mysqli_stmt_fetch($stmt)) {
      $retVal = [
          'ID'       => $id,
          'Name'     => $name,
          'IsActive' => $isactive
      ];
    }
    
    mysqli_stmt_close($stmt);

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
