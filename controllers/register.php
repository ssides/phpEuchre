<?php
   
    // Database connection
    include('config/db.php');
    
    if(isset($_POST["submit"])) {

        $name          = $_POST["name"];
        $password      = $_POST["password"];
        
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
          if(!empty($name) && !empty($password)){
            
            // clean the form data before sending to database
            $_name = mysqli_real_escape_string($connection, $name);
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
                // $playerID = com_create_guid();
                $PlayerID = "asdf";
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
                    $success_msg = 'Registration successful';
                }
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
          }
        }
    }
?>