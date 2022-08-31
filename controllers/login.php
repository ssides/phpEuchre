<?php
   
    // Database connection
    include('config/db.php');

    if(isset($_POST['login'])) {
        $name_signin      = $_POST['name_signin'];
        $password_signin  = $_POST['password_signin'];

        $_name = mysqli_real_escape_string($connection, $name_signin);
        $pswd = mysqli_real_escape_string($connection, $password_signin);
        $sqlErr = "post";

        // Query if email exists in db
        $sql = "select * from `Players` where `Name` = '{$_name}' ";
        $query = mysqli_query($connection, $sql);
        
        if(!$query){
            $sqlErr = mysqli_error($connection);
        } else {
            $rowCount = mysqli_num_rows($query);
            
            if($rowCount <= 0) {
                $accountNotExistErr = '<div class="alert alert-danger">
                        User account does not exist.
                    </div>';
            } 
        }

        
    }

?>