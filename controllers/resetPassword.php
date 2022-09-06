<?php
   
    // Database connection
    include('config/db.php');
    include('svc/removeCookie.php');
    
    removeCookie();
    
    if(isset($_POST["submit"])) {

        $name            = $_POST["name"];
        $password        = $_POST["password"];
        $confirmpassword = $_POST["confirmpassword"];
        
        $_name = mysqli_real_escape_string($connection, $name);
        $nameCheckQuery = mysqli_query($connection, "select * from `Player` where `Name` = '{$_name}' ");
        $nameCount = mysqli_num_rows($nameCheckQuery);
        
        if($nameCount == 0) {
            $errorMsg = '
                <div class="alert alert-danger" role="alert">
                    That user name does not exist.
                </div>
            ';
        } else {

          $id = ($row = mysqli_fetch_array($nameCheckQuery)) ? $id  = $row['ID'] : '';

          if(!empty($password) && !empty($confirmpassword)){
            
            if($confirmpassword == $password){
                // always clean the form data before sending to database
                $_password = mysqli_real_escape_string($connection, $password);
                
                $password_hash = password_hash($_password, PASSWORD_BCRYPT);

                $smt = mysqli_prepare($connection, 'update `Player` set `Password` = ? where `ID` = ?');
                mysqli_stmt_bind_param($smt, 'ss', $password_hash, trim($id));
                mysqli_stmt_execute($smt);
                
                if(mysqli_stmt_affected_rows($smt) > 0){
                    $successMsg = 'Password successfully reset. Please sign in.';
                } else {
                    $errorMsg = 'Error: '.mysqli_error($connection);
                }
                
                mysqli_stmt_close($smt);
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