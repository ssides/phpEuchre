<?php
  include_once('config/db.php');
  include_once('config/config.php');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo './content/css/site.css?v='.$version ?>">
  <title>Sides Family Euchre - Sound Manager</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="./content/ko/knockout-3.5.1.js"></script>
</head>

<body>

  <div class="App">
    <div class="vertical-center">
      <div class="inner-block">
        <button onclick="playSound()">Play Sound</button>
      </div>
    </div>
  </div>
  
  <?php
    include('content/js/partials/app.php');
    include('content/js/soundManager.php')
  ?>

</body>

</html>