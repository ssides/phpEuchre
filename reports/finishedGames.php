<?php include('authorize.php'); ?>
<?php include('controllers/dashboard.php'); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="../content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo '../content/css/site.css?v='.$version  ?>">
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
      <h3 class="card-title" data-bind="text: 'Games finished after ' + cutoffDate()"></h3>
      <table class="reportTable">
      <thead>
      <tr>
        <td>Finish Date</td>
        <td>Organizer</td>
        <td>Partner</td>
        <td>Score</td>
        <td>Left</td>
        <td>Right</td>
        <td>Score</td>
      </tr>
      </thead>
      <tbody data-bind="foreach: finishedGames">
      <tr>
        <td class="reportCell" data-bind="text: finishDate"></td>
        <td data-bind="text: organizer, class: organizerScore > opponentScore ? 'reportCell reportWinner' : 'reportCell'"></td>
        <td data-bind="text: partner, class: organizerScore > opponentScore ? 'reportCell reportWinner' : 'reportCell'" ></td>
        <td data-bind="text: organizerScore, class: organizerScore > opponentScore ? 'reportCell reportWinner' : 'reportCell'" ></td>
        <td data-bind="text: left, class: opponentScore > organizerScore ? 'reportCell reportWinner' : 'reportCell'"></td>
        <td data-bind="text: right, class: opponentScore > organizerScore ? 'reportCell reportWinner' : 'reportCell'" ></td>
        <td data-bind="text: opponentScore, class: opponentScore > organizerScore ? 'reportCell reportWinner' : 'reportCell'" ></td>
      </tr>
      </tbody>
      </table>
    </div>
  </div>
  <?php include('../content/js/reports/finishedGames.php') ?>
</body>
</html>