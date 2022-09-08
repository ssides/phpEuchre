<?php  
    include_once('config/db.php');
    include_once('config/config.php');
    
    if (empty($_COOKIE[$cookieName]) || empty($_SESSION['gameID'])) {
      header('Location: index.php');
    } else {
      $gameID = $_SESSION['gameID'];
      
      if(isset($_POST["inviteLeft"])) {
        $errorMsg = $_POST["leftID"]["id"];
        // $smt = mysqli_prepare($connection, 'update `Game` set `Partner` = ? where `ID` = ?');
        // mysqli_stmt_bind_param($smt, 'ss', $password_hash, trim($id));
        // mysqli_stmt_execute($smt);

        // if(mysqli_stmt_affected_rows($smt) <= 0){
          // $errorMsg = 'Error: '.mysqli_error($connection);
        // }

        // mysqli_stmt_close($smt);

      }
      
    }
    

?>