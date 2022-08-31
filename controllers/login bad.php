<?php
   
    // Database connection
    include('config/db.php');

    if(isset($_POST['login'])) {
        $name_signin      = $_POST['name_signin'];
        $password_signin  = $_POST['password_signin'];

        $_name = mysqli_real_escape_string($connection, $name_signin);
        $pswd = mysqli_real_escape_string($connection, $password_signin);

        // Query if email exists in db
        $sql = "select * from `Players` where `Name` = '{$_name}' ";
        $query = mysqli_query($connection, $sql);
        $rowCount = mysqli_num_rows($query);

        // If query fails, show the reason 
        if(!$query){
           $sqlErr = mysqli_error($connection));
        }

        if(!empty($name_signin) && !empty($password_signin)){
            // Check if email exist
            if($rowCount <= 0) {
                $accountNotExistErr = '<div class="alert alert-danger">
                        User account does not exist.
                    </div>';
            } else {
                // Fetch user data and store in php session
                while($row = mysqli_fetch_array($query)) {
                    $id            = $row['PlayerID'];
                    $firstname     = $row['Name'];
                    $pass_word     = $row['Password'];
                    $token         = $row['Token'];
                    $is_active     = $row['IsActive'];
                }

                // Verify password
                $password = password_verify($pswd, $pass_word);

                // Allow only verified user
                if($is_active == '1') {
                    if($email_signin == $email && $password_signin == $password) {
                       // header("Location: ./dashboard.php");
                       $sqlErr = "Credentials match";
                       
                       // this needs to go into a cookie.
                       // $_SESSION['id'] = $id;
                       // $_SESSION['firstname'] = $firstname;
                       // $_SESSION['lastname'] = $lastname;
                       // $_SESSION['email'] = $email;
                       // $_SESSION['mobilenumber'] = $mobilenumber;
                       // $_SESSION['token'] = $token;

                    } else {
                        $namePwdErr  =
                            '<div class="alert alert-danger">
                                Access denied.
                            </div>';
                    }
                } else {
                    $verificationRequiredErr = '<div class="alert alert-danger">
                            Account verification is required for login.
                        </div>';
                }

            }

        } else {
            if(empty($name_signin)){
                $name_empty_err = "<div class='alert alert-danger email_alert'>
                            Email not provided.
                    </div>";
            }
            
            if(empty($password_signin)){
                $pass_empty_err = "<div class='alert alert-danger email_alert'>
                            Password not provided.
                        </div>";
            }            
        }

    }

?>    