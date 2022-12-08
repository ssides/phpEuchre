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

  function getPlayerSkipped($position) {
    $s = ' ';
    if ($position == 'O') {
      $s = 'P';
    } else if ($position == 'L') {
      $s = 'R';
    } else if ($position == 'P') {
      $s = 'O';
    } else if ($position == 'R') {
      $s = 'L';
    } 
    return $s;
  }

?>