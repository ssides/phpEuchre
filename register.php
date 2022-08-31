<?php include('controllers/register.php'); ?>

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
   
    <?php include_once('config/config.php'); ?>
    <?php include('header.php'); ?>
    <div class="App">
        <div class="vertical-center">
            <div class="inner-block">
              <h3>Registration Error</h3>

              <?php echo $success_msg; ?>
              <?php if(isset($email_exist)){ echo $email_exist; }; ?>
              <?php echo $email_verify_err; ?>
              <?php echo $email_verify_success; ?>

            </div>
        </div>
    </div>

</body>

</html>