<?php
  include_once('config/db.php');
  include_once('config/config.php');
  require('authorize.php'); 
  include_once('svc/group.php');

  $isManager = isManager($$a['r'], $$a['k']);
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo './content/css/site.css?v='.$version ?>">
  <title>Sides Family Euchre - Manage Groups</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="./content/ko/knockout-3.5.1.js"></script>
</head>

<body>

  <?php include('header.php'); ?>

  <div class="App">
    <div class="vertical-center">
      <div class="inner-block manageGroupBlock">

        <div class="alert alert-danger" style="display:none" data-bind="visible: errorMessage().length > 0" >
          <div data-bind="text: errorMessage"></div>
        </div>

        <?php if(empty($$a['k']) || !$isManager): ?>
          <div class="org-border dashboardMargin">
            To use this page you need to be a manager of the group you are logged in to.
          </div>
        <?php else: ?>
          <div class="org-border dashboardMargin">
            <div style="display:none" data-bind="visible: requests().length == 0">
              There are no outstanding group join requests.
            </div>
            <table style="width: 100%">
              <tbody data-bind="foreach: requests">
                <tr>
                  <td>
                  Player '<span data-bind="text: name"></span>' wants to join your group.
                  </td>
                  <td>
                    <button id="accept" class="btn btn-outline-primary btn-sm" data-bind="click: $parent.serviceAccept">Accept</button> &nbsp;
                    <button id="deny" class="btn btn-outline-primary btn-sm" data-bind="click: $parent.serviceDeny">Deny</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <?php
    include('content/js/partials/app.php');
    include('content/js/manageGroups.php')
  ?>

</body>

</html>