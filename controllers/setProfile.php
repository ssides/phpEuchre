<?php  
    if(isset($_POST['upload'])) {
      $dbg0 = count($_FILES['profileImage']);
      $dbg1 = $_FILES['profileImage']['name'];
      $dbg2 = $_FILES['profileImage']['type'];
      $dbg3 = $_FILES['profileImage']['size'];
      $dbg4 = $_FILES['profileImage']['error'];
    }
?>