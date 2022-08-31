<?php
   
    // Database connection
    include('config/db.php');
    
    if(isset($_POST["submit"])) {

        $firstname     = $_POST["firstname"];
        $lastname      = $_POST["lastname"];
        $name          = $_POST["name"];
        $mobilenumber  = $_POST["mobilenumber"];
        $password      = $_POST["password"];
        
        $name_check_query = mysqli_query($connection, "select * from `Players` where `Name` = '{$name}' ");
        $nameCount = mysqli_num_rows($name_check_query);
        
        if($nameCount > 0) {
            $email_exist = '
                <div class="alert alert-danger" role="alert">
                    A user with that name already exists!
                </div>
            ';
        } else {
          // PHP validation
          // Verify if form values are not empty
          if(!empty($name) && !empty($password)){
            
               // clean the form data before sending to database
            $_first_name = mysqli_real_escape_string($connection, $firstname);
            $_last_name = mysqli_real_escape_string($connection, $lastname);
            $_name = mysqli_real_escape_string($connection, $name);
            $_mobile_number = mysqli_real_escape_string($connection, $mobilenumber);
            $_password = mysqli_real_escape_string($connection, $password);
            
             // perform validation
            if(!preg_match("/^[a-zA-Z ]*$/", $_first_name)) {
                $f_NameErr = '<div class="alert alert-danger">
                        Only letters and white space allowed.
                    </div>';
            }
            if(!preg_match("/^[a-zA-Z ]*$/", $_last_name)) {
                $l_NameErr = '<div class="alert alert-danger">
                        Only letters and white space allowed.
                    </div>';
            }
            
            if((preg_match("/^[a-zA-Z ]*$/", $_first_name)) 
              && (preg_match("/^[a-zA-Z ]*$/", $_last_name)) 
              ){
                // Generate random activation token
                $token = md5(rand().time());
                $playerID = com_create_guid();
                
                // Password hash
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                // Query
                $sql = "insert into `Players` (`PlayerID`, `Name`, `Password`, `Token`, `IsActive`, `InsertDate`) 
                  values ('{$playerID}','{$name}', '{$password_hash}', '{$token}', '1, now())";
                  
                // Create mysql query
                $insertResult = mysqli_query($connection, $sql);
                
                 if(!$insertResult){
                    $success_msg = mysqli_error($connection);
                } else {
                    $success_msg = 'Registration successful';
                }
            }
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