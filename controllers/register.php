<?php
  include('config/db.php');
  include('svc/services.php');

  unset($_SESSION['login_error']);
  
  if(isset($_POST["submit"])) {
    try {
      unset($_SESSION['register_error']);
      $success_msg = '';
      $name = $_POST["name"];
      $_name = mysqli_real_escape_string($connection, $name);
      
      $sql = "select `ID` from `Player` where `Name` = ?";
      $stmt = mysqli_prepare($connection, $sql);
      mysqli_stmt_bind_param($stmt, 's', $_name);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      $row_count = mysqli_stmt_num_rows($stmt);
            
      if($row_count > 0) {
        $_SESSION['register_error'] = "A user with that name already exists.";
      } else {
        // PHP validation
        // Verify if form values are not empty
        if(!empty($name)){
          $playerID = GUID();
          
          $sql = "insert into `Player` (`ID`, `Name`, `IsActive`, `InsertDate`) 
            values (?, ?, '1', now())";
          $stmt = mysqli_prepare($connection, $sql);
          mysqli_stmt_bind_param($stmt, 'ss', $playerID, $_name);
          if (mysqli_stmt_execute($stmt)) {
            $success_msg = 'Registration successful. Please sign in.';
          } else {
            $_SESSION['register_error'] = "Failed to create account.";
          }
        } else {
          $_SESSION['register_error'] = "Name cannot be blank.";
        }
      }
    } catch (Exception $e) {
        trigger_error($e->getMessage() . "\n" . $e->getTraceAsString(), E_USER_ERROR);
        $_SESSION['register_error'] = "An unexpected error occurred. Please try again.";
    }
  }
?>