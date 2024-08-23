<?php  
    include_once('config/db.php');
    include_once('config/config.php');
    include_once('');
    
    $controllerError = "";
    
    if (empty($_COOKIE[$cookieName]) || empty($_SESSION['gameID'])) {
      header('Location: index.php');
    } else if($_SERVER["REQUEST_METHOD"] === 'POST') {
      $gameID = $_SESSION['gameID'];
      if(isset($_POST['startGame'])) {
        
        if (saveGameStartInfo($$a['r'], $_SESSION['gameID'], $_POST['playTo'], $_POST['gameSpeed'])) {
          header('Location: play.php');
        } else {
          // $controllerError is displayed on the page through the view model.
        }
      }
    }
    
    function saveGameStartInfo($playerID, $gameID, $playTo, $gameSpeed) {
      global $connection, $controllerError;
      $result = true;
      
      $sql = "update `Game` set `GameStartDate` = now(),`PlayTo`={$playTo}, `Speed` = {$gameSpeed} where `Organizer` = '{$playerID}' and `ID` = '{$gameID}'";
      $result = mysqli_query($connection, $sql);
      if ($result === false) {
        $controllerError = mysqli_error($connection);
        mysqli_query($connection, "ROLLBACK;");
      } else {
        mysqli_query($connection, "COMMIT;");
      }
      return $result;
    }
?>