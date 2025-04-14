<?php 
  require('authorize.php'); 
  
  if (strpos($appUrl, "8080") === false) { trigger_error("replay-game-end"); }
  
  if (empty($_SESSION['gameID'])) {
    header('Location: replay-prompt.php');
  } else {
    $_SESSION['replayGameID'] = $_SESSION['gameID'];
    $_SESSION['replayFromActiveGame'] = 'false';
    header('Location: replay.php');
  }
?>

