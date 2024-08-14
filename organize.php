<?php
  include_once('authorize.php');
  include_once('controllers/organize.php');
  include_once('config/config.php');
  include_once('svc/group.php');
  
  if (!isset($group)) $group = parseGroup();

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo './content/css/site.css?v='.$version  ?>">
  <title>Organize a Game</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="./content/ko/knockout-3.5.1.js"></script>
</head>

<body>
  <!-- Header -->
  <?php include('header.php'); ?>

  <div class="App">
    <div class="vertical-center">
      <div class="inner-block gamePlay">
      
        <div class="alert alert-danger" style="display:none" data-bind="visible: errorMessage().length > 0" >
          <div data-bind="text: errorMessage"></div>
        </div>

        <p class="text" data-bind="visible: allPlayers() === false && !allPlayersJoined()">Select players.</p>
        <p class="text" data-bind="visible: allPlayers() && !allPlayersJoined()">Invite players.</p>
        <p class="text" data-bind="visible: allPlayers() && allPlayersJoined()">Start the game</p>
        <table>
          <tr>
            <td></td>
            <td>
              <div class="org-border">
                <span>Partner</span><br />
                <select data-bind="visible: !partnerInvited(), options: users, optionsText: 'name', value: selectedPartner, optionsCaption:'Select'"></select>
                <div data-bind="visible: partnerInvited">
                  <table>
                    <tr>
                      <td style="vertical-align: middle;">
                        <div data-bind="visible: partnerInvited() ? selectedPartner().thumbnailpath.length > 0 : false">
                          <img data-bind="attr: {src: partnerInvited() ? selectedPartner().thumbnailpath : ''}" />
                        </div>
                      </td>
                      <td style="vertical-align: middle;">&nbsp;<span class="notice" data-bind="text: partnerInvited() ? selectedPartner().name : ''"></span></td>
                    </tr>
                  </table>
                </div>
                <p class="notice" data-bind="visible: partnerJoined()">Joined</p>
              </div>
            </td>
            <td></td>
          </tr>
          <tr>
            <td>
              <div class="org-border">
                <span>Opponent Left</span><br />
                <select data-bind="visible: !leftInvited(), options: users, optionsText: 'name', value: selectedLeft, optionsCaption:'Select'"></select>
                <div data-bind="visible: leftInvited">
                  <table>
                    <tr>
                      <td style="vertical-align: middle;">
                        <div data-bind="visible: leftInvited() ? selectedLeft().thumbnailpath.length > 0 : false">
                          <img data-bind="attr: {src: leftInvited() ? selectedLeft().thumbnailpath : ''}" />
                        </div>
                      </td>
                      <td style="vertical-align: middle;">&nbsp;<span class="notice" data-bind="text: leftInvited() ? selectedLeft().name : ''"></span></td>
                    </tr>
                  </table>
                </div>
                <p class="notice" data-bind="visible: leftJoined()">Joined</p>
              </div>
            </td>
            <td></td>
            <td>
              <div class="org-border">
                <span>Opponent Right</span><br />
                <select data-bind="visible: !rightInvited(), options: users, optionsText: 'name', value: selectedRight, optionsCaption:'Select'"></select>
                <div data-bind="visible: rightInvited">
                  <table>
                    <tr>
                      <td style="vertical-align: middle;">
                        <div data-bind="visible: rightInvited() ? selectedRight().thumbnailpath.length > 0 : false">
                          <img data-bind="attr: {src: rightInvited() ? selectedRight().thumbnailpath : ''}" />
                        </div>
                      </td>
                      <td style="vertical-align: middle;">&nbsp;<span class="notice" data-bind="text: rightInvited() ? selectedRight().name : ''"></span></td>
                    </tr>
                  </table>
                </div>
                <p class="notice" data-bind="visible: rightJoined()">Joined</p>
              </div>
            </td>
          </tr>
          <tr>
            <td></td>
            <td>
              <div class="org-border"><p class="text">Organizer (you)</p></div>
            </td>
            <td></td>
          </tr>
        </table>
        <p class="error"><?php echo $errorMsg; ?></p>
        <div class="inviteMargin" data-bind="visible: allPlayers() & (!leftInvited() || !rightInvited() || !partnerInvited())">
          <button data-bind="click: inviteAll">Invite All Players</button>
        </div>

        <div class="org-border" data-bind="visible: allPlayersJoined()">
          <form action="" method="post">
            <table>
              <tr>
                <td><label for="playTo">Play to:&nbsp;&nbsp;</label></td>
                <td style="width: 54px"><input id="playTo" name="playTo" type="number" data-bind="value: playTo" style="width:50px" max="20" min="1"></td>
                <td></td>
              </tr>
              <tr>
                <td><label for="gameSpeed" class="form-label" data-bind="click: toggleSpeed">Game speed:&nbsp;</label></td>
                <td><input type="range" class="form-range" min="0" max="1" step="1" name="gameSpeed" id="gameSpeed" data-bind="value: gameSpeed"></td>
                <td><span data-bind="click: toggleSpeed">&nbsp;&nbsp;<label class="form-label" data-bind="html: displayGameSpeed"></label></span></td>
              </tr>
            </table>
            <button type="submit" name="startGame" id="startGame">Start Game</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php 
    include('content/js/partials/app.php');
    include('content/js/organize.php') 
    ?>
</body>

</html>