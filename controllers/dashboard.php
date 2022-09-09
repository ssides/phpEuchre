<?php 
    include_once('config/db.php');
    include_once('config/config.php');
    include_once('svc/GUID.php');
    
    if (empty($_COOKIE[$cookieName])) {
      header('Location: index.php');
    } else {
      if(isset($_POST['organize'])) {
        $gameID = startGame($_COOKIE[$cookieName]);
        if (!empty($gameID)) {
          header("Location: organize.php");
          $_SESSION['gameID'] = $gameID;

        }
      }
    }
    
    function startGame($playerID) {
      global $connection;
      $gameID = GUID();
      $orgScore = 0;
      $oppScore = 0;
      $smt = mysqli_prepare($connection, 'insert into `Game` (`ID` , `Organizer`, `OrganizerScore`, `OpponentScore`, `DateInserted`) values (?,?,?,?,now())');
      mysqli_stmt_bind_param($smt, 'ssii', $gameID, $playerID, $orgScore,$oppScore);
      if (!mysqli_stmt_execute($smt)){
        $sqlErr = mysqli_error($connection);
        $gameID = '';
      }
      mysqli_stmt_close($smt);

      return $gameID;
    }
?>