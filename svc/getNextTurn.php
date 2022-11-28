<?php

  function getNextTurn($position) {
    $turn = ' ';
    if ($position == 'O') {
      $turn = 'L';
    } else if ($position == 'L') {
      $turn = 'P';
    } else if ($position == 'P') {
      $turn = 'R';
    } else if ($position == 'R') {
      $turn = 'O';
    } 
    return $turn;
  }

?>