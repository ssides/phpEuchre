<?php 
  include('config/db.php');
  include('config/config.php');
  include('svc/getThumbnailURL.php');
  
  $user = getUserInfo($_COOKIE[$cookieName]);
  
  function getUserInfo($playerID) {
    global $connection;

    $user = array();
    $user['ID'] = '';
    $user['Name'] = 'not set';
    $user['ThumbnailURL'] = '';

    $sql = "select p.`ID`,`Name`,u.`ThumbnailPath`
        from `Player` p
        left join `UserProfile` u on u.`PlayerID` = p.`ID`
        where p.`ID` = '{$playerID}'";
    $results = mysqli_query($connection, $sql);

    while ($row = mysqli_fetch_array($results)) {
      $user['ID'] = $row['ID'];
      $user['Name'] = $row['Name'];
      $user['ThumbnailURL'] = is_null($row['ThumbnailPath']) ? '' : getThumbnailURL($row['ThumbnailPath']);
    }

    return $user;
  }
?>