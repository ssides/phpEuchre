<?php include('authorize.php'); ?>
<?php include('controllers/dashboard.php'); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="../content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo '../content/css/site.css?r='.mt_rand() ?>">
  <title>Start or Join a Game</title>
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
      <h3 class="card-title">100 Recent Started Games</h3>
      <table class="reportTable">
      <thead>
      <tr>
        <td>Game ID</td>
        <td>Cutoff Date</td>
        <td>Game Start Date</td>
        <td>Organizer</td>
        <td>Partner</td>
        <td>Left</td>
        <td>Right</td>
      </tr>
      </thead>
      <tbody data-bind="foreach: games">
      <tr>
        <td class="reportCell" data-bind="text: gameID"></td>
        <td class="reportCell" data-bind="text: cutoffDate"></td>
        <td class="reportCell" data-bind="text: gameStartDate"></td>
        <td class="reportCell" data-bind="text: oName"></td>
        <td class="reportCell" data-bind="text: pName"></td>
        <td class="reportCell" data-bind="text: lName"></td>
        <td class="reportCell" data-bind="text: rName"></td>
        
      </tr>
      </tbody>
      </table>
    </div>
  </div>
  <?php include('../content/js/reports/games.php') ?>
</body>
</html>