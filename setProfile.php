<?php include('authorize.php'); ?>
<?php include('controllers/setProfile.php'); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <title>Set Profile</title>
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

        <div class="row">
<div class="col-md-6">
        <form class="org-border" action="" method="post" enctype="multipart/form-data">
        <?php echo $sqlErr; ?>
        <?php echo $errorMsg; ?>
        <?php echo $successMsg; ?>
        <?php echo 'dbg0: '.$dbg0; ?><br>
        <?php echo $dbg1; ?><br>
        <?php echo 'dbg2: '.$dbg2; ?><br>
        <?php echo $dbg3; ?><br>

        <div class="form-group padding">
        <label>Profile Image</label>
        <input type="file" class="form-control" accept="image/*" name="profileImage" />
        </div>
        <button type="submit" name="upload" id="upload" class="btn btn-outline-primary btn-lg btn-block">Upload</button>
        </form>
        </div>
<div class="col-md-6">
        <form class="org-border" action="" method="post">
        <?php echo $sqlErr2; ?>
        <?php echo $errorMsg2; ?>
        <?php echo $successMsg2; ?>

        <!-- <img src="./controllers/thumbnail.php&r=<?php echo mt_rand(0, 65535) ?>" alt="Thumbnail"> 
        <br>
        <-php echo '<img src="'.$appUrl.'controllers/thumbnail.php&r='.mt_rand(0, 65535).'" alt="Thumbnail">'; ?>
        <--- <-php echo '<img src="'.$appUrl.'images/srs.jpg" alt="SRS">'; ?>  this works  ->
        <br>
        <-php echo '<img src="'.$appUrl.'controllers/tnt2.php" alt="SRS" />'; ?>
        -->
        <div class="form-group">
          <label>X Offset</label>
          <input type="number" class="form-control" name="xofs" id="xofs" />
        </div>
        <div class="form-group">
          <label>Y Offset</label>
          <input type="number" class="form-control" name="yofs" id="yofs" />
        </div>
        <div class="form-group">
          <label>Scale</label>
          <input type="number" class="form-control" name="scale" id="scale" />
        </div>
        <button type="submit" name="adjust" id="adjust" class="btn btn-outline-primary btn-lg btn-block">Adjust</button>
        </form>
        </div>

        </div>
        </div>
      </div>
    </div>

</body>

</html>