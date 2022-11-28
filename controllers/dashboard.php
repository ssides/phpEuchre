<?php 
    include_once('config/db.php');
    include_once('config/config.php');
    include_once('svc/services.php');
    
    if (empty($_COOKIE[$cookieName])) {
      header('Location: index.php');
    } else {
      if(isset($_POST['organize'])) {
        $gameID = startGame($_COOKIE[$cookieName]);
        if (!empty($gameID)) {
          $_SESSION['gameID'] = $gameID;
          header("Location: organize.php");
        }
      } else if (isset($_POST['join']) || isset($_POST['rejoin'])) {
          $_SESSION['gameID'] = $_POST['gameid'];
          
          if (joinGame($_POST['gameid'], $_COOKIE[$cookieName], $_POST['identifier'])) {
            header("Location: play.php");
          }
      }
    }
    
    function joinGame($gameID, $playerID, $identifier) {
      global $connection;
      
      $sql = "";
      switch($identifier) {
        case 'Partner':
          $sql = "update `Game` set `PartnerJoinDate` = now() where `Partner` = '{$playerID}' and `ID` = '{$gameID}'";
          break;
        case 'Opponent Left':
          $sql = "update `Game` set `LeftJoinDate` = now() where `Left` = '{$playerID}'and `ID` = '{$gameID}'";
          break;
        case 'Opponent Right':
          $sql = "update `Game` set `RightJoinDate` = now() where `Right` = '{$playerID}' and `ID` = '{$gameID}'";
          break;
        case 'Organizer':
          // there is nothing to do in this case.
          $sql = "";
          break;
        default:
          $sql = "";
      };
      
      if (strlen($sql) > 0) {
        return mysqli_query($connection, $sql);
      } else {
        return true;
      }

    }
    
    function startGame($playerID) {
      global $sqlErr,$connection;
      $gameID = GUID();
      $zero = 0;
      $smt = mysqli_prepare($connection, "insert into `Game` (`ID` , `Organizer`, `OrganizerScore`, `OpponentScore`, `OrganizerTricks`,`OpponentTricks`,`InsertDate`) values (?,?,?,?,?,?,now())");
      mysqli_stmt_bind_param($smt, 'ssiiii', $gameID, $playerID, $zero,$zero,$zero,$zero);
      if (!mysqli_stmt_execute($smt)){
        $sqlErr = mysqli_error($connection);
        $gameID = '';
      }
      mysqli_stmt_close($smt);

      return $gameID;
    }
?>