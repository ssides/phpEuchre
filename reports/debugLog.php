<?php include('authorize.php'); ?>
<?php include('controllers/dashboard.php'); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="../content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo '../content/css/site.css?v='.$version  ?>">
  <title>Debug Log Report</title>
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
      <h3 class="card-title">Debug Log</h3>
      <div>
        <label for="gameID">Game ID:&nbsp;</label><input name="gameID" size="40" maxlength="40" type="text" data-bind="value: gameID">
        <button data-bind="click: getLogData">Get Log</button>
        <br>
        <br>
      </div>
      <table class="reportTable">
      <thead>
      <tr>
        <td class="reportCell" >Position</td>
        <td class="reportCell" >State</td>
        <td class="reportCell" >Message</td>
        <td class="reportCell" >Opp Score</td>
        <td class="reportCell" >Opp Tricks</td>
        <td class="reportCell" >Org Score</td>
        <td class="reportCell" >Org Tricks</td>
        <td class="reportCell" >DealID</td>
        <td class="reportCell" >Dealer</td>
        <td class="reportCell" >Turn</td>
        <td class="reportCell" >CardFaceUp</td>
        <td class="reportCell" >ACO</td>
        <td class="reportCell" >ACP</td>
        <td class="reportCell" >ACL</td>
        <td class="reportCell" >ACR</td>
        <td class="reportCell" >PO</td>
        <td class="reportCell" >PP</td>
        <td class="reportCell" >PL</td>
        <td class="reportCell" >PR</td>
        <td class="reportCell" >Date</td>
      </tr>
      </thead>
      <tbody data-bind="foreach: log">
      <tr>
        <td class="reportCell" data-bind="text: positionID"></td>
        <td class="reportCell" data-bind="text: gameControllerState"></td>
        <td class="reportCell" data-bind="text: message"></td>
        <td class="reportCell" data-bind="text: opponentScore"></td>
        <td class="reportCell" data-bind="text: opponentTricks"></td>
        <td class="reportCell" data-bind="text: organizerScore"></td>
        <td class="reportCell" data-bind="text: organizerTricks"></td>
        <td class="reportCell" data-bind="text: dealID"></td>
        <td class="reportCell" data-bind="text: dealer"></td>
        <td class="reportCell" data-bind="text: turn"></td>
        <td class="reportCell" data-bind="text: cardFaceUp"></td>
        <td class="reportCell" data-bind="text: aco"></td>
        <td class="reportCell" data-bind="text: acp"></td>
        <td class="reportCell" data-bind="text: acl"></td>
        <td class="reportCell" data-bind="text: acr"></td>
        <td class="reportCell" data-bind="text: po"></td>
        <td class="reportCell" data-bind="text: pp"></td>
        <td class="reportCell" data-bind="text: pl"></td>
        <td class="reportCell" data-bind="text: pr"></td>
        <td class="reportCell" data-bind="text: insertDate"></td>
      </tr>
      </tbody>
      </table>
    </div>
  </div>
  <?php include('../content/js/reports/debugLog.php') ?>
</body>
</html>