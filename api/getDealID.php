<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $gameID = $_POST['gameID'];
      $response = array();
      $response['ErrorMsg'] = "";
      $response['DealID'] = "";

      $sql = "select `DealID` from `GameDeal` where `GameID` = '{$gameID}' and `IsActive` = '1'";

      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($connection);
      } else {
        if (mysqli_num_rows($results) > 1) {
          $response['ErrorMsg'] .= 'More than 1 active deal in GameDeal';
        } else {
          while ($row = mysqli_fetch_array($results)) {
            $response['DealID'] = $row['DealID'];
          }
        }
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