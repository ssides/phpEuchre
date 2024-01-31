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
        $gameID = $_POST['gameid'];
        $identifier = $_POST['identifier'];
        $playerID = $_COOKIE[$cookieName];
      
        $_SESSION['gameID'] = $gameID;
        
        if (isset($_POST['rejoin']) && $gameControllerLog) {
          $sqlErr = logRejoinGame($gameID, $playerID, $identifier);
        }
        
        if (strlen($sqlErr) == 0 && joinGame($gameID, $playerID, $identifier)) {
          header("Location: play.php");
        }
      }
    }
    
    function logRejoinGame($gameID, $playerID, $identifier){
      global $hostname, $username, $password, $dbname;
      $conn = mysqli_connect($hostname, $username, $password, $dbname);
      $err = "";

      $id = GUID();
      $positionID = getPositionID($identifier);
      $state = 'pre-play';
      $message = 'Rejoining game';
      
      $sql = "insert into `GameControllerLog` (`ID`,`GameID`,`PlayerID`,`PositionID`,`GameControllerState`,`Message`,`OrganizerScore`,`OpponentScore`,`OrganizerTricks`,`OpponentTricks`,`InsertDate`) values (?,?,?,?,?,?,0,0,0,0,now())";
      
      $smt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($smt, 'ssssss', $id,$gameID,$playerID,$positionID,$state,$message);
      if (!mysqli_stmt_execute($smt)) {
        $err = mysqli_error($conn);
      }
      
      mysqli_stmt_close($smt);
      mysqli_close($conn);
      
      return $err;
    }
    
    function getPositionID($identifier) {
      $posID = '-';
      
      switch($identifier) {
        case 'Partner':
          $posID = 'P';
          break;
        case 'Opponent Left':
          $posID = 'L';
          break;
        case 'Opponent Right':
          $posID = 'R';
          break;
        case 'Organizer':
          $posID = 'O';
          break;
        default:
          $posID = '-';
      }

      return $posID;
    }
    
    function joinGame($gameID, $playerID, $identifier) {
      global $connection;
      
      $result = true;
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
        mysqli_query($connection, "START TRANSACTION;");
        $result = mysqli_query($connection, $sql);
        mysqli_query($connection, "COMMIT;");
      }
      
      return $result;
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