<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST[$cookieName]) && isAuthenticated($_POST[$cookieName])) {

      $msg = "updating";
      if (isset($_POST['identifier'])) {
        $sql = '';
        if ($_POST['identifier'] == 'partner') {
          $sql = "update `Game` set `Partner` = '{$_POST['player']}', `PartnerInviteDate`=now() where `ID` = '{$_POST['gameID']}'";
        } else if ($_POST['identifier'] == 'left') {
          $sql = "update `Game` set `Left` = '{$_POST['player']}', `LeftInviteDate`=now() where `ID` = '{$_POST['gameID']}'";
        } else {
          $sql = "update `Game` set `Right` = '{$_POST['player']}', `RightInviteDate`=now() where `ID` = '{$_POST['gameID']}'";
        }
        $update = mysqli_query($connection, $sql);
        if ($update) {
          $msg = "OK";
        } else {
          $msg = mysqli_error($connection);
        }
      } else {
        $msg = "Identifier not set.";
      }
     
      http_response_code(200);
      echo $msg;

    } else {
      echo "ID invalid or missing.";
    }
  } else {
    echo "Expecting request method: POST";
  }
?>