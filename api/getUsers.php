<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getThumbnailURL.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isset($_POST['groupID']) && isAuthenticated($_POST['r'])) {

      $response = array();
      $users = array();
      
      mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
      $conn = mysqli_connect($hostname, $username, $password, $dbname);

      $pid = $_POST['r'];
      $gid = $_POST['groupID'];

      $sql = "select p.`ID`,substr(`Name`,1,8) `Name`,u.`ThumbnailPath`
          from `Player` p
          join `PlayerGroup` pg on p.`ID` = pg.`PlayerID` and pg.`GroupID` = ? and pg.`IsActive` = '1'
          left join `UserProfile` u on u.`PlayerID` = p.`ID`
          where p.`ID` <> ? and p.`IsActive` = '1'
          order by `Name`";

      $smt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($smt, 'ss', $gid, $pid);
      if (mysqli_stmt_execute($smt) === true) {
        mysqli_stmt_bind_result($smt, $id, $name, $thumbnail);
        while (mysqli_stmt_fetch($smt)) {
          $tnURL = is_null($thumbnail) ? '' : getThumbnailURL($thumbnail);
          array_push($users, array($id, $name, $tnURL));
        }
      }

      $response['Users'] = $users;
      
      http_response_code(200);
      echo json_encode($response);

    } else {
      http_response_code(500);
      echo "ID invalid or missing.";
    }
  } else {
    http_response_code(500);
    echo "Expecting request method: POST";
  }
?>