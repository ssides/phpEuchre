<?php 
  include_once('config/config.php');
  ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="./content/css/site.css">
    <title>Hello PHP</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="helloPadding">
    <?php 
      $thumbnailpath = 'C:/src/phpEuchre/images/E26DF607-3160-409B-B886-DF87ACF06306.png';
    ?>
    <img src="./profileImg.php?id=" alt="Thumbnail">
    <?php echo $_SESSION['gameID'].'<br>'; ?>
    <?php echo "Today is " . date("Y-m-d").'<br>' ; ?>
    <?php
      // $now = new DateTime(date("Y-m-d"));
      // $now->sub(new DateInterval('P3D'));
      // $startdate = $now->format('Y-m-d');
      // echo 'three days ago: '.$startdate.'<br>';
      // echo ' done<br>';
      echo $thumbnailpath.'<br>';
      echo strpos($thumbnailpath,'images').'<br>';
      $ofs = strpos($thumbnailpath,'images');
      echo substr($thumbnailpath, $ofs).'<br>';
      echo $appUrl.substr($thumbnailpath, $ofs).'<br>';
    ?>
    </div>

</body>

</html>