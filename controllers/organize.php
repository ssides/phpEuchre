<?php  
    include_once('config/db.php');
    include_once('config/config.php');
    
    if (empty($_COOKIE[$cookieName]) || empty($_SESSION['gameID'])) {
      header('Location: index.php');
    } else {
      $gameID = $_SESSION['gameID'];
      if(isset($_POST['startGame'])) {
        if (setGameStartDate($_COOKIE[$cookieName], $_SESSION['gameID'])) {
          header('Location: play.php');
        } 
        else {
          // todo: echo an error message instead of redirecting to index.
          header('Location: index.php');
        }
      }
    }
    
    function setGameStartDate($playerID, $gameID) {
      global $connection;
      
      $sql = "update `Game` set `GameStartDate` = now() where `Organizer` = '{$playerID}' and `ID` = '{$gameID}'";
      return mysqli_query($connection, $sql);
    }
?>