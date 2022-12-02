<?php

  function getAlone($cardFaceUp) {
    return strlen($cardFaceUp) == 5;
  }
  
  function getSkippedPosition($position) {
    switch($position) {
      case "O":
        return "P";
      case "P":
        return "O";
      case "L":
        return "R";
      case "R":
        return "L";
      default:
        return "-";
    }
  }

?>