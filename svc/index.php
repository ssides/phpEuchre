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
  <link rel="stylesheet" href="<?php echo './content/css/site.css?r='.mt_rand() ?>">
  <title>Hello PHP</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <style>
    .flip-container-p {
      perspective: 1000px;
    }
    .flip-container-p.hover .flipper-p {
      transform: rotateY(180deg);
    }
    .flip-container-p,
    .back-p,
    .front-p {
      width: 50px;
      height: 70px;
    }
    .flipper-p {
      transition: 0.5s;
      transform-style: preserve-3d;
      position: relative;
    }
    .back-p,
    .front-p {
      backface-visibility: hidden;
      position: absolute;
      top: 0;
      left: 0;
    }
    .back-p {
      z-index: 2;
      transform: rotateY(0deg);
    }
    .front-p {
      transform: rotateY(180deg);
    }
    
    .flip-container-l {
      perspective: 1000px;
    }
    .flip-container-l.hover .flipper-l {
      transform: rotateX(180deg);
    }
    .flip-container-l,
    .back-l,
    .front-l {
      width: 70px;
      height: 50px;
    }
    .flipper-l {
      transition: 0.5s;
      transform-style: preserve-3d;
      position: relative;
    }
    .back-l,
    .front-l {
      backface-visibility: hidden;
      position: absolute;
      top: 0;
      left: 0;
    }
    .back-l {
      z-index: 2;
      transform:  rotateX(0deg) ;
    }
    .front-l {
      transform: rotateX(180deg) ;
    }

  </style>
</head>

<body>

  <div class="helloPadding">
    <p>Server Document Root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?></p>
<br><br><br>
</body>

</html>