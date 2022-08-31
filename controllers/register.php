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
        if($rowCount == 0) {
            $email_exist = '
                <div class="alert alert-danger" role="alert">
                    User with email already exist!
                </div>
            ';
        } else {
          
        }
    }
?>