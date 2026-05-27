<?php include('controllers/register.php'); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo './content/css/site.css?v='.$version  ?>">
  <title>Register</title>
  <!-- jQuery + Bootstrap JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
</head>

<body>
   
    <?php include_once('config/config.php'); ?>
    <?php include('header.php'); ?>
    
    <div class="App">
        <div class="vertical-center">
            <div class="inner-block">
                <form action="" method="post">
                    <h3>Register</h3>

                    <?php echo $success_msg; ?>
                    <?php if(isset($_SESSION['register_error'])) {
                        echo('<div class="alert alert-danger">');
                        echo($_SESSION['register_error']);
                        echo('</div>');
                        } 
                    ?>
                    
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="name" />
                    </div>
                    </br>
                    <button type="submit" name="submit" id="submit" class="btn btn-outline-primary btn-lg btn-block">Sign up</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>