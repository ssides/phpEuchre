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

        <form action="" method="post" enctype="multipart/form-data">
          <?php echo $sqlErr; ?>
          <?php echo $errorMsg; ?>
          <?php echo $successMsg; ?>

      <?php echo '1: '.$dbg1.'<br>'; ?>
      <?php echo '2: '.$dbg2.'<br>'; ?>
      <?php echo '3: '.$dbg3.'<br>'; ?>
      <?php echo '4: '.$dbg4.'<br>'; ?>
      <?php echo '4: '.$dbg5.'<br>'; ?>

          <div class="form-group">
            <label>Profile Image</label>
            <input type="file" class="form-control" accept="image" name="profileImage" />
          </div>
          <button type="submit" name="upload" id="upload" class="btn btn-outline-primary btn-lg btn-block">Upload</button>
        </form>
        
      </div>
    </div>
  </div>

</body>

</html>