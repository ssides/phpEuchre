<?php 
  require('authorize.php'); 
  
  if (strpos($appUrl, "8080") === false) { trigger_error("replay-from-game"); }
  
  if (empty($_SESSION['gameID'])) {
    header('Location: replay-prompt.php');
  } else {
    $_SESSION['replayGameID'] = $_SESSION['gameID'];
    $_SESSION['replayFromActiveGame'] = 'true';
    header('Location: replay.php');
  }
?>

