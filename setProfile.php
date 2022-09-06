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
        <?php echo $sqlErr; ?>
        <?php echo $errorMsg; ?>
        <?php echo $successMsg; ?>
        <?php echo $dbg0; ?>
        <?php echo $dbg1; ?>
        <?php if(empty($thumbnailPath)): ?>
          <form action="" method="post" enctype="multipart/form-data">

            <div class="form-group padding">
              <label>Profile Image</label>
              <input type="file" class="form-control" accept="image/*" name="profileImage" />
            </div>
            <button type="submit" name="upload" id="upload" class="btn btn-outline-primary btn-lg btn-block">Upload</button>
          </form>
        <?php else: ?>
          <div class="row">
            <div class="col-md-4">
              &nbsp;
            </div>
            <div class="col-md-4">
              <img src="./thumbnail.php?r=<?php echo mt_rand(0, 65535) ?>" alt="Thumbnail">
              <br>
              <br>
            </div>
          </div>
        <?php endif; ?>
        
        <div class="row vpad">
          <div class="col-md-6">
            <form action="" method="post">
              <button type="submit" name="zoomin" id="zoomin" class="btn btn-outline-primary btn-lg btn-block">Zoom in</button>
            </form>
          </div>
          <div class="col-md-6">
            <form action="" method="post">
              <button type="submit" name="zoomout" id="zoomout" class="btn btn-outline-primary btn-lg btn-block">Zoom out</button>
            </form>
          </div>
        </div>
        
        <div class="row vpad">
          <div class="col-md-6">
            <form action="" method="post">
              <button type="submit" name="left" id="left" class="btn btn-outline-primary btn-lg btn-block">Left</button>
            </form>
          </div>
          <div class="col-md-6">
            <form action="" method="post">
              <button type="submit" name="right" id="right" class="btn btn-outline-primary btn-lg btn-block">Right</button>
            </form>
          </div>
        </div>
        <div class="row vpad">
          <div class="col-md-6">
            <form action="" method="post">
              <button type="submit" name="up" id="up" class="btn btn-outline-primary btn-lg btn-block">Up</button>
            </form>
          </div>
          <div class="col-md-6">
            <form action="" method="post">
              <button type="submit" name="down" id="down" class="btn btn-outline-primary btn-lg btn-block">Down</button>
            </form>
          </div>
        </div>
        <form class="vpad" action="" method="post">
          <button type="submit" name="change" id="change" class="btn btn-outline-primary btn-lg btn-block">Change Picture</button>
        </form>
        
      </div>
    </div>
  </div>

</body>

</html>