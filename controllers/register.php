<?php
   
    // Database connection
    include('config/db.php');
    
    if(isset($_POST["submit"])) {

        $firstname     = $_POST["firstname"];
        $lastname      = $_POST["lastname"];
        $email         = $_POST["email"];
        $mobilenumber  = $_POST["mobilenumber"];
        $password      = $_POST["password"];
        $email_verify_err =  $_POST["firstname"];
        
        // check if email already exist
        $email_check_query = mysqli_query($connection, "select * from `users` where email = '{$email}' ");
        $rowCount = mysqli_num_rows($email_check_query);
        // check if user email already exist
        if($rowCount > 0) {
            $email_exist = '
                <div class="alert alert-danger" role="alert">
                    User with email already exist!
                </div>
            ';
        } else {
          // PHP validation
          // Verify if form values are not empty
          if(!empty($firstname) && !empty($lastname) && !empty($email) && !empty($mobilenumber) && !empty($password)){
            
          } else {
              if(empty($firstname)){
                $fNameEmptyErr = '<div class="alert alert-danger">
                    First name can not be blank.
                </div>';
            }
            if(empty($lastname)){
                $lNameEmptyErr = '<div class="alert alert-danger">
                    Last name can not be blank.
                </div>';
            }
            if(empty($email)){
                $emailEmptyErr = '<div class="alert alert-danger">
                    Email can not be blank.
                </div>';
            }
            if(empty($mobilenumber)){
                $mobileEmptyErr = '<div class="alert alert-danger">
                    Mobile number can not be blank.
                </div>';
            }
            if(empty($password)){
                $passwordEmptyErr = '<div class="alert alert-danger">
                    Password can not be blank.
                </div>';
            }
          }
        }
    }
?>