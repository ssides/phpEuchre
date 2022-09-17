<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getThumbnailURL.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $ary = array();
      $sql = "select p.`ID`,substr(`Name`,1,8) `Name`,u.`ThumbnailPath`
              from `Player` p
              left join `UserProfile` u on u.`PlayerID` = p.`ID`
              where p.`ID` <> '{$_POST[$cookieName]}'";
      $results = mysqli_query($connection, $sql);

      while ($row = mysqli_fetch_array($results)) {
        $tnURL = is_null($row['ThumbnailPath']) ? '' : getThumbnailURL($row['ThumbnailPath']);
        array_push($ary, array($row['ID'],$row['Name'],$tnURL));
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