<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $ary = array();
      $results = mysqli_query($connection, 'select `ID`,`Name` from `Player`');

      while ($row = mysqli_fetch_array($results)) {
        array_push($ary, array($row['ID'],$row['Name']));
      }

      http_response_code(200);
      echo json_encode($ary);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
?>