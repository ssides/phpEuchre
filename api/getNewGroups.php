<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  // Select groups I'm not a member of and have no pending requests to join or the pending requests were all declined.
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $pid = $_POST['r'];
      $groups = [];
      
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