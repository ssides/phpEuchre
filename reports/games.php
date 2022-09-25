<?php include('authorize.php'); ?>
<?php include('controllers/dashboard.php'); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo './content/css/site.css?r='.mt_rand() ?>">
  <title>Start or Join a Game</title>
  <!-- jQuery + Bootstrap JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="./content/ko/knockout-3.5.1.js"></script>
</head>

<body>
  <div class="card">
    <div class="card-header">
      <?php include('header.php'); ?>
    </div>
    <div class="card-body">
      <br>
      <h3 class="card-title">Games</h3>
      <table>
      <thead>
      <tr><td></td></tr>
      </thead>
      </table>
    </div>
  </div>
</body>
</html>