<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
          
      mysqli_query($connection, "START TRANSACTION;");
      $smt = mysqli_prepare($connection, "update `Game` set `ScoringInProgress` = '0',`ACO` = null,`ACP` = null,`ACL` = null,`ACR` = null  where `ID`= ?");
      mysqli_stmt_bind_param($smt, 's', $gameID);
      if (!mysqli_stmt_execute($smt)){
        $response['ErrorMsg'] .= mysqli_error($connection);
        mysqli_query($connection, "ROLLBACK;");
      } else {
        mysqli_query($connection, "COMMIT;");
      }

      mysqli_stmt_close($smt);

      http_response_code(200);
      
      echo json_encode($response);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>