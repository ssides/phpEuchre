<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getThumbnailURL.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {
      
      $user = array();
      $sql = "select `ID`,`Name`,u.`ThumbnailPath`
              from `Player` p
              left join `UserProfile` u on u.`PlayerID` = p.`ID`
              where `ID` = '{$_POST['r']}'";
      $results = mysqli_query($connection, $sql);

      while ($row = mysqli_fetch_array($results)) {
        $user['ID'] = $row['ID'];
        $user['Name'] = $row['Name'];
        $user['ThumbnailURL'] = is_null($row['ThumbnailPath']) ? '' : getThumbnailURL($row['ThumbnailPath'])
      }

      http_response_code(200);
      
      echo json_encode($user);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
  
?>