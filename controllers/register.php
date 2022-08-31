<?php
   
    // Database connection
    include('config/db.php');
    
    function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
    
    if(isset($_POST["submit"])) {

        $name          = $_POST["name"];
        $password      = $_POST["password"];
        $confirmpassword=$_POST["confirmpassword"];
        
        $name_check_query = mysqli_query($connection, "select * from `Players` where `Name` = '{$name}' ");
        $nameCount = mysqli_num_rows($name_check_query);
        
        if($nameCount > 0) {
            $name_exist = '
                <div class="alert alert-danger" role="alert">
                    A user with that name already exists!
                </div>
            ';
        } else {
          // PHP validation
          // Verify if form values are not empty
          if(!empty($name) && !empty($password) && !empty($confirmpassword)){
            
            // clean the form data before sending to database
            $_name = mysqli_real_escape_string($connection, $name);
            $_password = mysqli_real_escape_string($connection, $password);
            
            if($confirmpassword == $password){
                // Generate random activation token
                $token = md5(rand().time());
                
                $playerID = GUID();
                
                // Password hash
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                // Query
                $sql = "insert into `Players` (`PlayerID`, `Name`, `Password`, `Token`, `IsActive`, `InsertDate`) 
                  values ('{$playerID}','{$name}', '{$password_hash}', '{$token}', '1', now())";
                  
                // Create mysql query
                $insertResult = mysqli_query($connection, $sql);
                
                 if(!$insertResult){
                    $success_msg = mysqli_error($connection);
                } else {
                    $success_msg = 'Registration successful. Please sign in.';
                }
            } else {
                $passwordEmptyErr = '<div class="alert alert-danger">
                    Confirm Password does not match Password.
                </div>';
            }
          } else {
             
            if(empty($name)){
                $nameEmptyErr = '<div class="alert alert-danger">
                    Name cannot be blank.
                </div>';
            }
           
            if(empty($password)){
                $passwordEmptyErr = '<div class="alert alert-danger">
                    Password cannot be blank.
                </div>';
            }
            if(empty($confirmpassword)){
                $confirmpasswordEmptyErr = '<div class="alert alert-danger">
                    Confirm Password cannot be blank.
                </div>';
            }
          }
        }
    }
?>