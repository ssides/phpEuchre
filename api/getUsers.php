<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  include('../svc/getThumbnailURL.php');

  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {
      
      $response = array();
      $response['ErrorMsg'] = "";
      $users = array();
      $sql = "select p.`ID`,substr(`Name`,1,8) `Name`,u.`ThumbnailPath`
              from `Player` p
              left join `UserProfile` u on u.`PlayerID` = p.`ID`
              where p.`ID` <> '{$_POST[$cookieName]}' and p.`IsActive` = '1'
              order by `Name`";
              
      $results = mysqli_query($connection, $sql);
      if ($results === false) {
        $response['ErrorMsg'] .= mysqli_error($connection);
      } else {
        while ($row = mysqli_fetch_array($results)) {
          $tnURL = is_null($row['ThumbnailPath']) ? '' : getThumbnailURL($row['ThumbnailPath']);
          array_push($users, array($row['ID'],$row['Name'],$tnURL));
        }
      }
      
      $response['Users'] = $users;
      
      http_response_code(200);
      echo json_encode($response);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
?>