<?php
  include_once('config/db.php');
  include_once('config/config.php');
  require('authorize.php');
  include('controllers/setProfile.php');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?php echo './content/css/site.css?v='.$version  ?>">
  <title>Set Profile</title>
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

        <div class="alert alert-danger" style="display:none" data-bind="visible: errorMessage().length > 0" >
          <div data-bind="text: errorMessage"></div>
        </div>
        <div class="row" style="display: none;" data-bind="visible: thumbnailurl().length > 0">
          <div class="row col-12">
            <table>
              <tr>
                <td colspan="5" style="padding: 20px;">
                  <span class="profileText">Use the buttons to make adjustments to your profile picture.</span>
                </td>
              </tr>
              <tr>
                <td></td>
                <td class="userProfileCell">
                  <button type="button" name="up" id="up" class="btn btn-outline-primary btn-sm btn-block" data-bind="click: up">
                    <img class="profileArrowSize" src="<?php echo $appUrl.'content/images/profile/up.png'; ?>" alt="Up Arrow">
                  </button>
                </td>
                <td></td>
                <td rowspan="3">
                  <button type="button" name="zoomin" id="zoomin" class="btn btn-outline-primary btn-sm btn-block" style="width: 30px;" data-bind="click: zoomin">+</button>
                  <button type="button" name="zoomout" id="zoomout" class="btn btn-outline-primary btn-sm btn-block" style="width: 30px;" data-bind="click: zoomout">-</button>
                </td>
              </tr>
              <tr>
                <td>
                  <button type="button" name="left" id="left" class="btn btn-outline-primary btn-sm btn-block float-end" data-bind="click: left">
                    <img class="profileArrowSize" src="<?php echo $appUrl.'content/images/profile/left.png'; ?>" alt="Left Arrow">
                  </button>
                </td>
                <td class="userProfileCell">
                  <img data-bind="attr: {src: thumbnailurl() }" />
                </td>
                <td>
                  <button type="button" name="right" id="right" class="btn btn-outline-primary btn-sm btn-block" data-bind="click: right">
                    <img class="profileArrowSize" src="<?php echo $appUrl.'content/images/profile/right.png'; ?>" alt="Right Arrow">
                  </button>
                </td>
                <td></td>
              </tr>
              <tr>
                <td></td>
                <td class="userProfileCell">
                  <button type="button" name="down" id="down" class="btn btn-outline-primary btn-sm btn-block" data-bind="click: down">
                    <img class="profileArrowSize" src="<?php echo $appUrl.'content/images/profile/down.png'; ?>" alt="Down Arrow">
                  </button>
                </td>
                <td>
                </td>
                <td>
                  <button type="button" name="change" id="change" class="btn btn-outline-primary btn-sm btn-block profileButtonText" data-bind="click: change">Change Picture</button>
                </td>
              </tr>
              <tr>
                <td colspan="5" style="padding: 10px;">&nbsp;</td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row" style="display: none;" data-bind="visible: thumbnailurl().length == 0 && pageReady()">
          <div class="row col-12">
            <form action="" method="post" enctype="multipart/form-data">
              <div class="mb-3 profilePadding">
                <label for="profileImage" class="form-label">Profile Image</label>
                <input class="form-control" type="file" id="profileImage" name="profileImage">
              </div>
              <button type="submit" name="upload" id="upload" class="btn btn-outline-primary btn-lg btn-block">Upload</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
        
  <?php
    include('content/js/partials/app.php');
    include('content/js/setProfile.php')
  ?>

</body>

</html>