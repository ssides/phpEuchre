<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  // Select groups I'm not a member of and have no pending requests to join or the pending requests were all declined.
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $pid = $_POST['r'];
      $groups = array();
      
      // If there is an error, an exception will be thrown, and the api will return with response code 500.
      // I need to set it up so that when I'm running locally, errors are displayed on the screen, and when
      // I'm running in the cloud, errors are logged.  None of that is set up.  I should have set that up
      // first before using prepared statements.
      mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
      $conn = mysqli_connect($hostname, $username, $password, $dbname);
      
      // `PlayerGroup` has an IsActive column that is only set by running SQL updates. 
      // Maybe that column should be dropped.  I'm not sure whether or not I should
      // include that column in these joins.
      $sql = "select g.`ID`,g.`Description` from `Group` g
        left join `GroupRequest` gr on g.`ID` = gr.`GroupID` and gr.`PlayerID` = ? and (gr.`IsActive` = 'R' or gr.`IsActive` = 'A')
        left join `PlayerGroup` pgr on g.`ID` = pgr.`GroupID` and pgr.`PlayerID` = ?
        where gr.`GroupID` is null and pgr.`GroupID` is null and g.`IsActive` = '1'";

      $smt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($smt, 'ss', $pid, $pid);
      if (mysqli_stmt_execute($smt) === true) {
        mysqli_stmt_bind_result($smt, $id, $description);
        while (mysqli_stmt_fetch($smt)) {
          array_push($groups, array($id, $description));
        }
      }

      $response['Groups'] = $groups;
      
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