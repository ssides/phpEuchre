<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  // Select group join requests for the group I am logged in to.
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $gid = $_POST['groupID'];
      $requests = array();
      
      mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
      $conn = mysqli_connect($hostname, $username, $password, $dbname);
      
      $sql = "select p.`ID`,p.`Name` 
        from `GroupRequest` gr 
        join `Player` p on gr.`PlayerID` = p.`ID`
        where gr.`GroupID` = ? and gr.`IsActive` = 'R'";

      $smt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($smt, 's', $gid);
      if (mysqli_stmt_execute($smt) === true) {
        mysqli_stmt_bind_result($smt, $id, $name);
        while (mysqli_stmt_fetch($smt)) {
          array_push($requests, array($id, $name));
        }
      }

      $response['Requests'] = $requests;
      
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