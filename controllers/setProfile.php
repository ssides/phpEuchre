<?php  
    if(isset($_POST['upload'])) {
      $dbg0 = count($_FILES["profileImage"]);
      for ($i = 0; $i < $dbg0; $i++) {
        $dbg1 = $dbg1.$_FILES["profileImage"][$i];
      }
    }
?>