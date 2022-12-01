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
  <!-- Header -->
  <?php include('header.php'); ?>

  <div class="App">
    <div class="vertical-center">
      <div class="inner-block">
        <?php echo $sqlErr; ?>
        <div class="org-border dashboardMargin" data-bind="visible: invitations().length > 0">
          <table style="width: 100%">
            <tbody data-bind="foreach: invitations">
              <tr>
                <td><span data-bind="text: organizerName"></span> is inviting you to play as <span data-bind="text: position"></span>.</td>
                <td>
                  <form action="" method="post">
                    <input type="hidden" name="gameid" id="gameid" data-bind="value: gameID">
                    <input type="hidden" name="identifier" id="identifier" data-bind="value: position">
                    <button type="submit" name="join" id="join" class="btn btn-outline-primary">Join</button>
                  </form>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="dashboardRight"><button class="uxRefreshInvites btn btn-outline-secondary btn-sm">Refresh</button></div>
        </div>
        <div class="org-border dashboardMargin" data-bind="visible: rejoinGames().length > 0">
          <table>
            <tbody data-bind="foreach: rejoinGames">
              <tr>
                <td>Would you like to return to the game organized by <span data-bind="text: organizerName"></span>? You were playing as <span data-bind="text: position"></span>.</td>
                <td>
                  <form action="" method="post">
                    <input type="hidden" name="gameid" id="gameid" data-bind="value: gameID">
                    <input type="hidden" name="identifier" id="identifier" data-bind="value: position">
                    <button type="submit" name="rejoin" id="rejoin" class="btn btn-outline-primary">Return</button>
                  </form>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="dashboardRight"><button class="uxRefreshReJoins btn btn-outline-secondary btn-sm">Refresh</button></div>
        </div>
        <form action="" method="post">
          <table>
            <tr>
              <td>
                <button type="submit" name="organize" id="organize" class="btn btn-outline-primary btn-md btn-block">Start a Game</button>
              </td>
              <td><div class="dashboardPadding">You will be the organizer of the game.</div></td>
            </tr>
          </table>
        </form>
      </div>
    </div>
  </div>
  <?php include('content/js/dashboard.php') ?>
</body>

</html>