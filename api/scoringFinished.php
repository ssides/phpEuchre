<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      
      $conn = mysqli_connect($hostname, $username, $password, $dbname);

      mysqli_query($conn, "START TRANSACTION;");
      
      $sql = "update `Game` set `ScoringInProgress` = '0',`ACO` = null,`ACP` = null,`ACL` = null,`ACR` = null  where `ID`= '{$gameID}'";
      
      if (mysqli_query($conn, $sql) === false) {
        $response['ErrorMsg'] .= mysqli_error($conn);
        mysqli_query($conn, "ROLLBACK;");
      } else {
        mysqli_query($conn, "COMMIT;");
      }
      
      http_response_code(200);
      
      echo json_encode($response);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>