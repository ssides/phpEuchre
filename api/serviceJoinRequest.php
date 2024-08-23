<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/services.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      if (isset($_POST['groupID']) && isset($_POST['playerID']) && isset($_POST['code']) && !empty($_POST['groupID']) && !empty($_POST['playerID']) && !empty($_POST['code'])) {
        $gid = $_POST['groupID'];
        $pid = $_POST['playerID'];
        $code = $_POST['code'];
        
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn = mysqli_connect($hostname, $username, $password, $dbname);
        
        $smt = mysqli_prepare($conn, "update `GroupRequest` set IsActive = ? where `PlayerID` = ? and `GroupID` = ?");
        mysqli_stmt_bind_param($smt, 'sss', $code, $pid, $gid);
        mysqli_stmt_execute($smt);
        mysqli_stmt_close($smt);

        if ($code == 'A') {
          $smt = mysqli_prepare($conn, "insert into `PlayerGroup` (`ID`,`PlayerID`,`GroupID`,`IsActive`,`InsertDate`) values (?,?,?,'1',now())");
          mysqli_stmt_bind_param($smt, 'sss', GUID(), $pid, $gid);
          mysqli_stmt_execute($smt);
          mysqli_stmt_close($smt);
        }
        
        http_response_code(200);
        echo "";
        
      } else {
        http_response_code(500);
        echo "Argument missing.";
      }
    } else {
      http_response_code(500);
      echo "ID invalid or missing.";
    }
  } else {
    http_response_code(500);
    echo "Expecting request method: POST";
  }

?>