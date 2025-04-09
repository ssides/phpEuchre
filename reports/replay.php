<?php require('authorize.php'); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="../content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo '../content/css/site.css?v='.$version  ?>">
  <link rel="stylesheet" href="<?php echo '../content/css/cards'.$cardFaces.'.css?v='.$version ?>">
  <link rel="stylesheet" href="<?php echo '../content/css/reports/replay.css?v='.$version  ?>">
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
      <div style="height: 58px">&nbsp;</div>

      <div class="App" >
        <div class="vertical-center" >
          <div class="inner-block justify-content-center replayWidth">
            <table class="replayWidth">
              <tr>
                <td>
                  <div id="WhatsTrump">
                    <div class="whatsTrumpContainer" style="display:none" data-bind="visible: trumpURL().length > 0">
                      <img class="whatsTrump" data-bind="attr: {src: trumpURL() }"/>
                    </div>
                  </div>
                </td>
                <td>
                  <div id="PartnerInfo">
                    <?php include('../partials/reports/replay/playerInfo.php'); ?>
                  </div>
                </td>
                <td>
                  <div id="OpponentScore" class="score">
                    <div class="scoreBorder">
                      <?php include('../partials/reports/replay/score.php'); ?>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div id="LeftInfo">
                    <?php include('../partials/reports/replay/playerInfo.php'); ?>
                  </div>
                </td>
                <td>
                  <div id="PlayTable">
                    <table class="playTable">
                      <thead>
                        <tr>
                          <td style="width: 75px; text-alight:right;"></td>
                          <td style="width: 54px;"></td>
                          <td style="width: 75px; text-alight:left"></td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr style="height: 58px">
                          <td>
                          </td>
                          <td>
                            <div data-bind="visible: partnerCardURL().length > 0">
                              <img data-bind="attr: {src: partnerCardURL}" class="cardImageSize" style="rotate: 180deg;" />
                            </div>
                          </td>
                          <td></td>
                        </tr>
                        <tr style="height: 42px">
                          <td>
                        <div data-bind="visible: leftCardURL().length > 0">
                          <img data-bind="attr: {src: leftCardURL }" class="cardImageSize" style="rotate: 90deg; transform: translate(0,-11px);" />
                        </div>
                          </td>
                          <td>
                            <div id="cardFaceUp" style="display:none" >
                              <img class="centerCardImageSize" />
                            </div>
                          </td>
                          <td>
                            <div data-bind="visible: rightCardURL().length > 0">
                              <img data-bind="attr: {src: rightCardURL }" class="cardImageSize" style="rotate: -90deg; transform: translate(0,11px);" >
                            </div>
                          </td>
                        </tr>
                        <tr style="height: 58px">
                          <td></td>
                          <td>
                            <div data-bind="visible: organizerCardURL().length > 0">
                              <img data-bind="attr: {src: organizerCardURL }" class="cardImageSize" >
                            </div>
                          </td>
                          <td></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </td>
                <td>
                  <div style="height: 168px">
                    <div style="height: 68px">&nbsp;</div>
                    <div id="RightInfo">
                      <?php include('../partials/reports/replay/playerInfo.php'); ?>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div id="OrganizerScore" class="score">
                    <div class="scoreBorder">
                      <?php include('../partials/reports/replay/score.php'); ?>
                    </div>
                  </div>
                </td>
                <td colspan=2>
                  <div id="OrganizerInfo">
                    <?php include('../partials/reports/replay/playerInfo.php'); ?>
                  </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan=3>
                  <div id="Controls">
                    <div class="controlsBorder controlsFS">
                      <div class="row">
                        <div class="col-4 cardFaceUpBorder" style="display:none;" data-bind="visible: showCardFaceUpBlock">
                          <table>
                            <tr>
                              <td style="vertical-align: top;"><span data-bind="text: cardFaceUpMessage"></span></td>
                              <td>
                                <ul class="list-group list-group-horizontal" style="margin: 0 0 0 10px; padding: 0;">
                                  <li class="list-group-item p-0" style="height:29px; width: 29px">
                                    <div class="cardSelectionContainer">
                                      <div style="margin: 0; padding: 0;" class="cardContainer" >
                                        <img class="clipCard" data-bind="attr: {src: cardFaceUpURL()}" />
                                      </div>
                                    </div>
                                  </li>
                                </ul>
                              </td>
                            </tr>
                          </table>
                        </div>
                        <div class="col-2 text-nowrap">
                          <span data-bind="text: handMessage"></span>
                        </div>
                        <div class="col-2 text-nowrap">
                          <span data-bind="text: message"></span>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-3">
                          <button id="previousHand" type="button" class="btn btn-primary replay-xsm" style="display:none" data-bind="click: previousHand, visible: showPreviousHandButton">Previous&nbsp;Hand</button>
                        </div>
                        <div class="col-3">
                          <button id="nextHand" type="button" class="btn btn-primary replay-xsm" style="display:none" data-bind="click: nextHand, visible: showNextHandButton">Next&nbsp;Hand</button>
                        </div>
                        <div class="col-3">
                          <button id="playCard" type="button" class="btn btn-primary replay-xsm" data-bind="click: playCard">Play&nbsp;Card</button>
                        </div>
                        <div class="col-3">
                          <button id="playTrick" type="button" class="btn btn-primary replay-xsm" data-bind="click: playTrick">Play&nbsp;Trick</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <?php
    include('../content/js/partials/app.php');
    include('../content/js/partials/cardSort.php');
    include('../content/js/reports/replay/partials/playViewModel.php');
    include('../content/js/reports/replay/partials/replayViewModel.php');
    include('../content/js/reports/replay/partials/scoreViewModel.php');
    include('../content/js/reports/replay/partials/playerViewModel.php');
    include('../content/js/reports/replay/partials/whatsTrumpViewModel.php');
    include('../content/js/reports/scores.php');
    include('../content/js/reports/replay/replay.php');

  ?>
</body>
</html>
