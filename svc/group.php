<?php 

  function setGroup($group) {
    $_SESSION['group'] = "ID={$group['ID']}&Description={$group['Description']}";
  }
  
  function parseGroup() {
    $g = array();
    parse_str($_SESSION['group'], $g);
    return $g;
  }

  function isManager($playerID, $groupID) {
    global $controllerError, $hostname, $username, $password, $dbname;
    $retVal = false;
    
    if (!empty($groupID)) {
      try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn = mysqli_connect($hostname, $username, $password, $dbname);

        $smt = mysqli_prepare($conn, "select `ID` from `Group` where `ManagerID` = ?");
        mysqli_stmt_bind_param($smt, 's', $playerID);
        if (mysqli_stmt_execute($smt) === true) {
          mysqli_stmt_store_result($smt);
          if (mysqli_stmt_num_rows($smt) > 0) {
            $retVal = true;
          }
        }
        mysqli_stmt_close($smt);
      } catch (Exception $e) {
        $controllerError .= 'group: '.$e->getMessage();
      }
    }
    
    return $retVal;
  }

?>