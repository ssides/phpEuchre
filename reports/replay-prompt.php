<?php require('authorize.php'); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="../content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo '../content/css/site.css?v='.$version  ?>">
  <title>Replay Report</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="../content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="../content/ko/knockout-3.5.1.js"></script>
</head>

<body>
  <div class="card">
    <div class="card-header">
      <?php include('header.php'); ?>
    </div>
    <div class="card-body">
      <br>
      <h3 class="card-title">Replay Report</h3>
      <div>
        <label for="gameID">Game ID:&nbsp;</label><input id="gameID" name="gameID" size="40" maxlength="40" type="text" data-bind="value: gameID">
        <button data-bind="click: gotoReplay">Replay</button>
        <br>
        <br>
      </div>
    </div>
  </div>
  <?php include('../content/js/reports/replay-prompt.php') ?>
</body>
</html>
