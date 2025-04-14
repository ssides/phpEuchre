<?php 
  require('authorize.php'); 
  
  if (empty($_SESSION['gameID'])) {
    header('Location: replay-prompt.php');
  } else {
    $_SESSION['replayGameID'] = $_SESSION['gameID'];
    $_SESSION['replayFromActiveGame'] = 'false';
    header('Location: replay.php');
  }
?>

