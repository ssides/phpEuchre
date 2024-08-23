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
  <link rel="stylesheet" href="<?php echo './content/css/site.css?v='.$version  ?>">
  <link rel="stylesheet" href="<?php echo './content/css/testStyles.css?v='.$version  ?>">
  <title>Hello PHP</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  
</head>

<body>

  <div class="helloPadding">
  <?php 
    $$d = array();
    $$d['r'] = "46C058D6-695B-47EF-AD84-095D16D020A2";
    $$d['k'] = "16052C21-7E42-466B-BB56-9D3FEDD3E1AF";
    
    $e = base64_encode(serialize($$d));
    echo $e;
    echo '<br>';

    $$a = unserialize(base64_decode($e));
    echo 'r: '.$$a['r'];
    echo '<br>';
    echo 'k: '.$$a['k'];
    echo '<br>';
  ?>
  </div>
  
</body>

</html>