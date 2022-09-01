<?php include('controllers/resetPassword.php'); ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <title>Register</title>
    <!-- jQuery + Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>

<body>
   
    <?php include('header.php'); ?>
    
    <div class="App">
        <div class="vertical-center">
            <div class="inner-block">
                <form action="" method="post">
                    <h3>Reset Password</h3>

                    <?php echo $successMsg; ?>
                    <?php echo $errorMsg; ?>
                    <?php echo 'execute: '.$dbg0; ?>
                    <?php echo 'id: '.$dbg1; ?>
                    <?php echo 'affected rows: '.$dbg2; ?>

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="name" />

                        <?php echo $nameEmptyErr; ?>
                    </div>

                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" class="form-control" name="password" id="password" />

                        <?php echo $_passwordErr; ?>
                        <?php echo $passwordEmptyErr; ?>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" name="confirmpassword" id="confirmpassword" />
                    </div>

                    <button type="submit" name="submit" id="submit" class="btn btn-outline-primary btn-lg btn-block">Sign up</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>