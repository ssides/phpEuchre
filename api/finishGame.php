<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $gameID = $_POST['gameID'];
      
      $sql = "update `Game` set `GameFinishDate` = now(), `Dealer` = null, `Turn` = null, `CardFaceUp` = null where `ID`='{$gameID}'";

      $conn = mysqli_connect($hostname, $username, $password, $dbname);

      mysqli_query($conn, "START TRANSACTION;");
      
      $results = mysqli_query($conn, $sql);
      if ($results === false) {
        $response['ErrorMsg'] = mysqli_error($conn);
        mysqli_query($conn, "ROLLBACK;");
      } else {
        mysqli_query($conn, "COMMIT;");
      }

      mysqli_close($conn);

      http_response_code(200);
      
      echo json_encode($response);
      
    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }

?>