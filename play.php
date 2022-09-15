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
  <link rel="stylesheet" href="./content/css/site.css">
  <title>Sides Family Euchre - Play</title>
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
        <p class="fs-6">Please be patient ... work in progress.</p>
        <table>
          <tr><td></td><td></td><td></td></tr>
          <tr>
            <td></td>
            <td>
              <table class="dbgTable" style="width: 175px; height: 168px; background-color: #006F2D">
                <thead>
                  <tr>
                    <td style="width: 65px; text-alight:right;">
                    </td>
                    <td style="width: 44px;">
                    </td>
                    <td style="widht: 65px; text-alight:left">
                    </td>
                  </tr>
                </thead>
                <tbody>
                  <tr style="height: 58px">
                    <td></td>
                    <td>
                      <div id="sfeN" style="display:none" class="flip-container-p">
                        <div class="flipper-p">
                          <div class="back-p">
                            <img src="<?php echo $appUrl; ?>content/images/cards/cardback.jpg" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;">
                          </div>
                          <div class="front-p">
                            <img src="<?php echo $appUrl; ?>content/images/cards/AS.jpg" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;">
                          </div>
                        </div>
                      </div>
                      <div id="sfeJN" data-bind="visible: nCardURL().length > 0">
                        <img data-bind="attr: {src: nCardURL }" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: 180deg;" />
                      </div>
                    </td>
                    <td></td>
                  </tr>
                  <tr style="height: 42px">
                    <td>
                      <div id="sfeW" style="display:none" class="flip-container-l" ">
                        <div class="flipper-l">
                          <div class="back-l">
                            <img src="<?php echo $appUrl; ?>content/images/cards/cardback.jpg" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: 90deg;">
                          </div>
                          <div class="front-l">
                            <img src="<?php echo $appUrl; ?>content/images/cards/AC.jpg" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: 90deg; transform: translate(<?php echo $cardImageWidth; ?>px,0);">
                          </div>
                        </div>
                      </div>
                      <div id="sfeJW" data-bind="visible: wCardURL().length > 0">
                        <img data-bind="attr: {src: wCardURL }" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: 90deg; transform: translate(0,-14px);" />
                      </div>
                    </td>
                    <td></td>
                    <td>
                      <div id="sfeE" style="display:none" class="flip-container-l">
                        <div class="flipper-l">
                          <div class="back-l">
                            <img src="<?php echo $appUrl; ?>content/images/cards/cardback.jpg" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: -90deg;">
                          </div>
                          <div class="front-l">
                            <img src="<?php echo $appUrl; ?>content/images/cards/AH.jpg" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: -90deg; transform: translate(-<?php echo $cardImageWidth; ?>px,0); ">
                          </div>
                        </div>
                      </div>
                      <div id="sfeJE" data-bind="visible: eCardURL().length > 0">
                        <img data-bind="attr: {src: eCardURL }" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px; rotate: -90deg; transform: translate(0,14px);" />
                      </div>
                    </td>
                  </tr>
                  <tr style="height: 58px">
                    <td></td>
                    <td>
                      <div id="sfeS" style="display:none" class="flip-container-p">
                        <div class="flipper-p">
                          <div class="back-p">
                            <img src="<?php echo $appUrl; ?>content/images/cards/cardback.jpg" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;">
                          </div>
                          <div class="front-p">
                            <img src="<?php echo $appUrl; ?>content/images/cards/AS.jpg" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;">
                          </div>
                        </div>
                      </div>
                      <div id="sfeJS" data-bind="visible: sCardURL().length > 0">
                        <img data-bind="attr: {src: sCardURL }" style="width:<?php echo $cardImageWidth; ?>px;height:<?php echo $cardImageHeight; ?>px;" />
                      </div>
                    </td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </td>
            <td></td>
          </tr>
          <tr><td></td><td></td><td></td></tr>
        </table>
      </div>
    </div>
  </div>

  <?php include('content/js/play.php') ?>

</body>

</html>