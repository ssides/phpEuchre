<?php include('controllers/login.php'); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Sides Family Euchre</title>
    <!-- jQuery + Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>

<body>

    <!-- Header -->
    <?php include('header.php'); ?>

    <div class="App">
        <div class="vertical-center">
          <div class="inner-block">

                <form action="" method="post">
                    <h3>Login</h3>

                    <?php echo $sqlErr; ?>
                    <?php echo $accountNotExistErr; ?>
                    <?php echo $namePwdErr; ?>
                    <?php echo $verificationRequiredErr; ?>
                    <?php echo $name_empty_err; ?>
                    <?php echo $pass_empty_err; ?>

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name_signin" id="name_signin" />
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password_signin" id="password_signin" />
                    </div>

                    <button type="submit" name="login" id="sign_in" class="btn btn-outline-primary btn-lg btn-block">Sign in</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>