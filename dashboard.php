<?php 
  require('authorize.php'); 
  include('controllers/dashboard.php'); 
  include_once('svc/group.php');
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo './content/css/site.css?v='.$version  ?>">
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
          <div data-bind="foreach: invitations">
            <div class="row w-100">
              <div class="col-10"><span data-bind="text: organizerName"></span> is inviting you to play as <span data-bind="text: position"></span>.
              </div>
              <div class="col-2">
                <form action="" method="post">
                  <input type="hidden" name="gameid" id="gameid" data-bind="value: gameID">
                  <input type="hidden" name="identifier" id="identifier" data-bind="value: position">
                  <button type="submit" name="join" id="join" class="btn btn-outline-primary">Join</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        
        <div class="org-border dashboardMargin" data-bind="visible: rejoinGames().length > 0">
          <div data-bind="foreach: rejoinGames">
            <div class="row w-100" >
              <div class="col-8">
                Would you like to return to the game organized by <span data-bind="text: organizerName"></span>? You were playing as <span data-bind="text: position"></span>.
              </div>
              <div class="col-2">
                <form action="" method="post">
                  <input type="hidden" name="gameid" id="gameid" data-bind="value: gameID">
                  <input type="hidden" name="identifier" id="identifier" data-bind="value: position">
                  <button type="submit" name="rejoin" id="rejoin" class="btn btn-outline-primary">Return</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        
        <div class="org-border dashboardMargin container" data-bind="visible: endgameGames().length > 0">
          <div data-bind="foreach: endgameGames">
            <div class="row w-100" >
              <div class="col-9">
                End the game you organized</br>on <span data-bind="text: insertDate"></span>?</br>The score is <span data-bind="text: organizerScore"></span> to <span data-bind="text: opponentScore"></span>.
              </div>
              <div class="col-2">
                <form action="" method="post">
                <input type="hidden" name="gameid" id="gameid" data-bind="value: gameID">
                <button type="submit" name="endgame" id="endgame" class="btn btn-outline-primary">End&nbsp;game</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <?php if(empty($$a['k'])): ?>
          <div class="org-border dashboardMargin">
            To start a game, log in to a group.
          </div>
        <?php else: ?>
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
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php include('content/js/dashboard.php') ?>
</body>

</html>