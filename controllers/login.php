<?php
    $accountNotExistErr = 'default';
   
    // Database connection
    include('config/db.php');
    include('config/config.php');
    
    function setLoginCookie($id) {
      global $cookieName;
      $result = false;
      if (function_exists('setcookie') === true)
      {
        if (setcookie($cookieName, $id)) {
          $result = true;
        } else {
        }
      }
      return $result;
    }
    
    if(isset($_POST['login'])) {
        $name_signin      = $_POST['name_signin'];
        $password_signin  = $_POST['password_signin'];

        $_name = mysqli_real_escape_string($connection, $name_signin);
        $pswd = mysqli_real_escape_string($connection, $password_signin);

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
            } else {
                if(!empty($name_signin) && !empty($password_signin)){
                    while($row = mysqli_fetch_array($query)) {
                        $id          = $row['PlayerID'];
                        $name        = $row['Name'];
                        $pass_word   = $row['Password'];
                        $token       = $row['Token'];
                        $is_active   = $row['IsActive'];
                    }
                    
                    $password = password_verify($pswd, $pass_word);
                    
                    if($pswd == $password) {
                      // credentials match.
                      if(setLoginCookie($id) === true) {
                        $accountNotExistErr = 'credentials match. cookie set'
                        header("Location: ./dashboard.php");
                      } else {
                        $sqlErr = "Could not log in. id: " . $id;
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
        }

        
    }

?>