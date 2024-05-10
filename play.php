<?php
  include('authorize.php');
  include('controllers/play.php');
  include_once('config/config.php');
 ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo './content/css/site.css?v='.$version ?>">
  <title>Sides Family Euchre - Play</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="./content/ko/knockout-3.5.1.js"></script>
</head>

<body>

  <?php include('header.php'); ?>

  <div class="App">
    <div class="vertical-center">
      <div class="inner-block justify-content-center">
        <table>
          <tr>
            <td>
              <div id="WhatsTrump">
                <div class="whatsTrumpContainer" data-bind="visible: trumpURL().length > 0">
                  <img class="whatsTrump" data-bind="attr: {src: trumpURL() }"/>
                </div>
              </div>
            </td>
            <td>
              <div id="NorthInfo">
                <?php include('partials/playerInfo.php'); ?>
              </div>
            </td>
            <td>
              <div id="MyScore" class="score">
                <div class="scoreBorder">
                  <?php include('partials/score.php'); ?>
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div id="WestInfo">
                <?php include('partials/playerInfo.php'); ?>
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
                        <div id="sfeN" style="display:none" class="flip-container-p">
                          <div class="flipper-p">
                            <div class="back-p">
                              <img src="<?php echo $appUrl; ?>content/images/cards/cardback.png" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;">
                            </div>
                            <div class="front-p">
                              <img src="<?php echo $appUrl; ?>content/images/cards/AS.png" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;">
                            </div>
                          </div>
                        </div>
                        <div data-bind="visible: nCardURL().length > 0">
                          <img data-bind="attr: {src: nCardURL}" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: 180deg;" />
                        </div>
                      </td>
                      <td></td>
                    </tr>
                    <tr style="height: 42px">
                      <td>
                        <div id="sfeW" style="display:none" class="flip-container-l" ">
                          <div class="flipper-l">
                            <div class="back-l">
                              <img src="<?php echo $appUrl; ?>content/images/cards/cardback.png" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: 90deg;">
                            </div>
                            <div class="front-l">
                              <img src="<?php echo $appUrl; ?>content/images/cards/AC.png" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: 90deg; transform: translate(<?php echo $cardImageWidth; ?>px,0);">
                            </div>
                          </div>
                        </div>
                        <div data-bind="visible: wCardURL().length > 0">
                          <img data-bind="attr: {src: wCardURL }" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: 90deg; transform: translate(0,-11px);" />
                        </div>
                      </td>
                      <td>
                        <div id="cardFaceUp" style="display:none" data-bind="visible: faceupCardURL().length > 0">
                          <img data-bind="attr: {src: faceupCardURL }" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;" />
                        </div>

                        <!-- this works on every device except iPad.  :-(
                        <div id="cardFaceUp" style="display:none" class="flip-container-p">
                          <div class="flipper-p">
                            <div class="back-p">
                              <img src="<?php echo $appUrl; ?>content/images/cards/cardback.png" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;">
                            </div>
                            <div class="front-p">
                              <img data-bind="attr: {src: faceupCardURL }" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;">
                            </div>
                          </div>
                        </div>
                        -->

                      </td>
                      <td>
                        <div id="sfeE" style="display:none" class="flip-container-l">
                          <div class="flipper-l">
                            <div class="back-l">
                              <img src="<?php echo $appUrl; ?>content/images/cards/cardback.png" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: -90deg;">
                            </div>
                            <div class="front-l">
                              <img src="<?php echo $appUrl; ?>content/images/cards/AH.png" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: -90deg; transform: translate(-<?php echo $cardImageWidth; ?>px,0); ">
                            </div>
                          </div>
                        </div>
                        <div data-bind="visible: eCardURL().length > 0">
                          <img data-bind="attr: {src: eCardURL }" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: -90deg; transform: translate(0,11px);" />
                        </div>
                      </td>
                    </tr>
                    <tr style="height: 58px">
                      <td></td>
                      <td>
                        <div id="sfeS" style="display:none" class="flip-container-p">
                          <div class="flipper-p">
                            <div class="back-p">
                              <img src="<?php echo $appUrl; ?>content/images/cards/cardback.png" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;">
                            </div>
                            <div class="front-p">
                              <img src="<?php echo $appUrl; ?>content/images/cards/AS.png" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;">
                            </div>
                          </div>
                        </div>
                        <div data-bind="visible: sCardURL().length > 0">
                          <img data-bind="attr: {src: sCardURL }" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;" />
                        </div>
                      </td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </td>
            <td>
              <div id="EastInfo">
                <?php include('partials/playerInfo.php'); ?>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div id="OpponentScore" class="score">
                <div class="scoreBorder">
                  <?php include('partials/score.php'); ?>
                </div>
              </div>
            </td>
            <td colspan=2>
              <div id="SouthInfo">
                <div class="info container" data-bind="class: isMyTurn() ? 'infoTurnBorder': iamSkipped() ? 'infoSkippedBorder' : 'infoBorder'">
                  <div class="row">
                    <div class="col-1">
                      <div class="dealerBorder" data-bind="visible: dealer() != ' '">
                        &nbsp;<span class="dealer" data-bind="text: dealer"></span>
                      </div>
                    </div>
                    <div class="col-1" data-bind="visible: trumpURL().length > 0">
                      <div data-bind="visible: trumpURL().length > 0">
                        <img data-bind="attr: {src: trumpURL() }" style="height: 15px; width: 15px;" />
                      </div>
                    </div>
                    <div class="col">
                      <ul class="list-group list-group-horizontal" data-bind="foreach: sortedCards">
                        <li class="list-group-item p-0" style="height:29px; width: 29px" data-bind="click: $parent.selectCard">
                          <div class="cardSelectionContainer">
                            <div class="cardSelecter" data-bind="class: isSelected() ? 'cardSelected' : ''">
                              <div style="margin: 2px; padding: 0px;" class="cardContainer" data-bind="class: isPlayable() ? '' : 'cardNotPlayable'">
                                <img class="clipCard" data-bind="attr: {src: url}" />
                              </div>
                            </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                    <div class="col">
                      <button id="discard" type="button" style="display:none" data-bind="visible: showDiscardBtn, enable: enableDiscardBtn, click: discard">Discard</button>
                      <button id="play" type="button" style="display:none" data-bind="visible: showPlayBtn, enable: enablePlayBtn, click: play">Play</button>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-3 pt-1">
                      <button id="pass" type="button" style="display:none" data-bind="visible: showPassBtn, enable: enablePassBtn, click: pass">Pass</button>
                    </div>
                    <div class="col-4 pt-1">
                      <button id="pickitup" type="button" style="display:none" data-bind="visible: showPickItUpGroup, enable: enablePickItUpGroup, click: pickItUp">Pick it up</button>
                    </div>
                    <div class="col-3 pt-1">
                      <div class="form-check aloneMargin" data-bind="visible: showPickItUpGroup">
                        <input class="form-check-input" type="checkbox" id="alone" data-bind="checked: obsAlone, enable: enablePickItUpGroup" name="alone" value="alone" />
                        <label class="form-check-label" for="alone" id="lblAlone" data-bind="enable: enablePickItUpGroup">Alone</label>
                      </div>
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

  <!-- Modal -->
  <div class="modal fade" id="bidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Choose Trump</h1>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col">
              <ul class="list-group list-group-horizontal" data-bind="foreach: suits">
                <li class="list-group-item p-0" data-bind="click: $parent.selectSuit">
                  <div class="suitContainer">
                    <div data-bind="class: isSelected() ? 'cardSelected' : ''">
                      <div class="suitSelecter" data-bind="class: isPlayable() ? '' : 'cardNotPlayable'">
                        <img class="suitSize" data-bind="attr: {src: url}" />
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
              <div class="ms-3 mt-1">
                <div class="form-check form-check-inline">
                  <label id="lblAlone">
                    <input id="alone2" type="checkbox" name="alone2" value="alone" data-bind="checked: alone" />&nbsp;Alone</label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="row">
              <div class="col-6">
                <button id="bidmodalpass" type="button" class="btn btn-primary" data-bind="visible: showPassBtn, enable: enablePassBtn, click: pass">Pass</button>
              </div>
              <div class="col-6">
                <button id="bidmodalsubmit" type="button" class="btn btn-primary" data-bind="visible: showSubmitBtn, enable: enableSubmitBtn, click: submit">Submit</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modals -->
  <div class="modal fade" id="finishGameModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Game Over</h1>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col">
              <div>
                <div>Winner:</div>
                <span data-bind="text: winner()"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div>
                <span data-bind="text: loser()"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="endGameDate">
                <span data-bind="text: dateStr()"></span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bind="click: ok">OK</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="endGameModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Game Over</h1>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col">
              <div>
                The organizer has ended the game.<br /><br />
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="app.gameEnded()" >OK</button>
          </div>
        </div>
      </div>
    </div>
  </div>


  <?php
  include('content/js/partials/app.php');
  include('content/js/partials/gameModel.php');
  include('content/js/partials/currentPlayerInfoViewModel.php');
  include('content/js/partials/whatsTrumpViewModel.php');
  include('content/js/partials/playerInfoViewModel.php');
  include('content/js/partials/scoreViewModel.php');
  include('content/js/partials/playViewModel.php');
  include('content/js/partials/bidDialogViewModel.php');
  include('content/js/partials/finishGameDialogViewModel.php');

  include('content/js/play.php')
  ?>

</body>

</html>