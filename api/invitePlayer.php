<?php 
  include_once('../config/db.php');
  include_once('../config/config.php');
  include('../controllers/isAuthenticated.php');
  
  if($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['r']) && isAuthenticated($_POST['r'])) {

      $msg = "updating";
      if (isset($_POST['identifier'])) {
        
        $sql = '';
        switch($_POST['identifier']) {
          case 'partner':
            $sql = "update `Game` set `Partner` = '{$_POST['player']}', `PartnerInviteDate`=now() where `ID` = '{$_POST['gameID']}'";
            break;
          case 'left':
            $sql = "update `Game` set `Left` = '{$_POST['player']}', `LeftInviteDate`=now() where `ID` = '{$_POST['gameID']}'";
            break;
          case 'right':
            $sql = "update `Game` set `Right` = '{$_POST['player']}', `RightInviteDate`=now() where `ID` = '{$_POST['gameID']}'";
            break;
          default:
            $msg = "Invalid identifier";
        };
        
        mysqli_query($connection, "START TRANSACTION;");
        $update = mysqli_query($connection, $sql);
        if ($update === false) {
          $msg = mysqli_error($connection);
          mysqli_query($connection, "ROLLBACK;");
        } else {
          $msg = "OK";
          mysqli_query($connection, "COMMIT;");
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