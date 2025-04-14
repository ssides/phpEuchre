<?php
  include_once('config/db.php');
  include_once('config/config.php');
  require('authorize.php'); 
  include('controllers/setProfile.php'); ?><!doctype html>
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
      
        <div class="row">
          <div class = "col-12">
            <form class="float-end" action="" method="post">
              <button type="submit" class="btn-close" aria-label="Close" name="close" id="close"></button>
            </form>
          </div>
        </div>
        
        <div class="alert alert-danger" style="display:none" data-bind="visible: errorMessage().length > 0" >
          <div data-bind="text: errorMessage"></div>
        </div>
        
        <?php if(empty($thumbnailPath)): ?>
        <form action="" method="post" enctype="multipart/form-data">
          <div class="mb-3 profilePadding">
            <label for="profileImage" class="form-label">Profile Image</label>
            <input class="form-control" type="file" id="profileImage" name="profileImage">
          </div>
          <button type="submit" name="upload" id="upload" class="btn btn-outline-primary btn-lg btn-block">Upload</button>
        </form>
        <?php else: ?>
        <div class="row">
          <div class="col-md-12">
            <table>
              <tr>
                <td></td>
                <td class="userProfileCell">

                  <form action="" method="post">
                    <button type="submit" name="up" id="up" class="btn btn-outline-primary btn-sm btn-block">
                      <div class="bi bi-arrow-up"></div>
                    </button>
                  </form>

                </td>
                <td></td>
                <td rowspan="3">
                  <form action="" method="post" title="Zoom In">
                    <button type="submit" name="zoomin" id="zoomin" class="btn btn-outline-primary btn-sm btn-block" style="width: 30px;">+</button>
                  </form>
                  <form action="" method="post" title="Zoom Out">
                    <button type="submit" name="zoomout" id="zoomout" class="btn btn-outline-primary btn-sm btn-block" style="width: 30px;">-</button>
                  </form>
                </td>
              </tr>
              <tr>
                <td>
                  <form action="" method="post">
                    <button type="submit" name="left" id="left" class="btn btn-outline-primary btn-sm btn-block">
                      <div class="bi bi-arrow-left"></div>
                    </button>
                  </form>
                </td>
                <td class="userProfileCell">
                  <img src="./thumbnail.php?r=<?php echo mt_rand(0, 65535) ?>" alt="Thumbnail">
                </td>
                <td>
                  <form action="" method="post">
                    <button type="submit" name="right" id="right" class="btn btn-outline-primary btn-sm btn-block">
                      <div class="bi bi-arrow-right"></div>
                    </button>
                  </form>
                </td>
                <td></td>
              </tr>
              <tr>
                <td></td>
                <td class="userProfileCell">
                  <form action="" method="post">
                    <button type="submit" name="down" id="down" class="btn btn-outline-primary btn-sm btn-block">
                      <div class="bi bi-arrow-down"></div>
                    </button>
                  </form>
                </td>
                <td>
                </td>
                <td></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <form class="vpad" action="" method="post">
              <button type="submit" name="change" id="change" class="btn btn-outline-primary btn-sm btn-block">Change Picture</button>
            </form>
          </div>
        </div>
        <?php endif; ?>
        
        </br>
        <div class="container border">
          <div class="row">
            <form action="" method="post">
              <table class="profilePadding"> 
                <tr>
                  <td><label for="selectgroup">Group Name</label></td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>                
                    <select id="selectgroup" name="selectgroup" data-bind="options: groups, optionsText: 'description', value: selectedGroup, optionsCaption:'Select'"></select>
                      <input type="hidden" data-bind="value: selectedGroup() ? selectedGroup().description : ''" id="group_description" name="group_description" />
                      <input type="hidden" data-bind="value: selectedGroup() ? selectedGroup().id : ''" id="group_id" name="group_id" />
                  </td>
                  <td class="profileTablePadding">
                    <button type="submit" name="joinrequest" id="joinrequest" class="btn btn-outline-primary btn-sm btn-block">Request to Join</button>
                  </td>
                </tr>
              </table>
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