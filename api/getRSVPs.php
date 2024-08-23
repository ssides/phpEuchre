<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {

      $game = array();
      $sql = "select `LeftJoinDate`,`RightJoinDate`,`PartnerJoinDate` from `Game` where `ID` = '{$_POST['gameID']}'";
      $results = mysqli_query($connection, $sql);
      
      while ($row = mysqli_fetch_array($results)) {
        $game['LeftJoinDate'] = is_null($row['LeftJoinDate']) ? '' : $row['LeftJoinDate'];
        $game['RightJoinDate'] = is_null($row['RightJoinDate']) ? '' : $row['RightJoinDate'];
        $game['PartnerJoinDate'] = is_null($row['PartnerJoinDate']) ? '' : $row['PartnerJoinDate'];
      }
     
      http_response_code(200);
      echo json_encode($game);

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
?>