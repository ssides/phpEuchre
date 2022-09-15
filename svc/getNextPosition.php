<?php 
  // the order is:
  // L)eft, P)artner, R)ight, O)rganizer
  function getNextPosition($currentPosition) {
    $next = '';
    
    switch($currentPosition) {
      case 'O':
        $next = 'L';
        break;
      case 'L':
        $next = 'P';
        break;
      case 'P':
        $next = 'R';
        break;
      case 'R':
        $next = 'O';
        break;
      default:
        $next = 'L';
    }
    return $next;
  }
?>