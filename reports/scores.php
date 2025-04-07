<?php require('authorize.php'); ?>
<?php include('controllers/dashboard.php'); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="../content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo '../content/css/site.css?v='.$version  ?>">
  <title>Scores Report</title>
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
      <h3 class="card-title">Score Report</h3>
      <div>
        <label for="gameID">Game ID:&nbsp;</label><input id="gameID" name="gameID" size="40" maxlength="40" type="text" data-bind="value: gameID">
        <button data-bind="click: getScores">Get Scores</button>
        <br>
        <br>
      </div>
      <table class="reportTable">
      <thead>
      <tr>
        <td class="reportCell" >Organizer Trump</td>
        <td class="reportCell" >Opponent Trump</td>
        <td class="reportCell" >Lead</td>
        <td class="reportCell" >O</td>
        <td class="reportCell" >L</td>
        <td class="reportCell" >P</td>
        <td class="reportCell" >R</td>
        <td class="reportCell" >Org Score</td>
        <td class="reportCell" >Opp Score</td>
        <td class="reportCell" >Org Tricks</td>
        <td class="reportCell" >Opp Tricks</td>
        <td class="reportCell" >Card Code</td>
        <td class="reportCell" >Dealer</td>
        <td class="reportCell" >DealID</td>
      </tr>
      </thead>
      <tbody data-bind="foreach: scores">
      <tr>
        <td class="reportCell" data-bind="text: organizerTrump"></td>
        <td class="reportCell" data-bind="text: opponentTrump"></td>
        <td class="reportCell" data-bind="text: lead"></td>
        <td class="reportCell" data-bind="text: cardO"></td>
        <td class="reportCell" data-bind="text: cardL"></td>
        <td class="reportCell" data-bind="text: cardP"></td>
        <td class="reportCell" data-bind="text: cardR"></td>
        <td class="reportCell" data-bind="text: organizerScore"></td>
        <td class="reportCell" data-bind="text: opponentScore"></td>
        <td class="reportCell" data-bind="text: organizerTricks"></td>
        <td class="reportCell" data-bind="text: opponentTricks"></td>
        <td class="reportCell" data-bind="text: cardFaceUp"></td>
        <td class="reportCell" data-bind="text: dealer"></td>
        <td class="reportCell" data-bind="text: dealID"></td>
      </tr>
      </tbody>
      </table>
    </div>
  </div>
  <?php include('../content/js/reports/scores.php') ?>
  <?php include('../content/js/reports/scoresViewModel.php') ?>
</body>
</html>