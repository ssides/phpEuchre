<?php 
  include_once('config/db.php');
  include_once('config/config.php');
  include('controllers/login.php'); 
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo './content/css/site.css?v='.$version ?>">
  <title>Sides Family Euchre</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="./content/ko/knockout-3.5.1.js"></script>
</head>

<body>
  <?php if (strpos($appUrl, "8080") === false) { trigger_error("index.php"); } ?>

  <?php include('header.php'); ?>

  <div class="App">
    <div class="vertical-center">
      <div class="inner-block">
        <form id="loginForm" action="" method="post">
          <h3>Login</h3>

          <div class="alert alert-danger" style="display:none" data-bind="visible: errorMessage().length > 0" >
              <div data-bind="text: errorMessage"></div>
          </div>
          <table>
            <tr>
              <td style="width: 80px"><label for="name_signin">Name</label></td>
              <td><input type="text" name="name_signin" id="name_signin" data-bind="value: name" /><span class="requiredField">&nbsp;*</span></td>
            </tr>
            <tr>
              <td><label for="password_signin">Password</label></td>
              <td><input type="password" name="password_signin" id="password_signin" data-bind="value: password" /><span class="requiredField">&nbsp;*</span></td>
            </tr>
            <tr>
              <td><label for="selectgroup">Group</label></td>
              <td class="sfeTooltip">
                <select id="selectgroup" data-bind="options: groups, optionsText: 'description', value: selectedGroup, optionsCaption:'Select'"></select>
                <input type="hidden" data-bind="value: selectedGroup() ? selectedGroup().description : ''" id="group_signin" name="group_signin" />
                <input type="hidden" data-bind="value: selectedGroup() ? selectedGroup().id : ''" id="group_id" name="group_id" />
                <span class="sfeTooltiptext">Selecting a group will give you the option of starting a game.</span>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <span class="requiredField">*&nbsp;Required field</span>
              </td>
            </tr>
          </table>
          </br>
          <button type="submit" name="login" id="sign_in" class="btn btn-outline-primary btn-lg btn-block" data-bind="click: validateSubmit">Sign in</button>
          </br>
          </br>
          <?php echo '<a href="'.$appUrl.'resetPassword.php">Forgot password?</a>'; ?>
        </form>
      </div>
    </div>
  </div>
  
  <?php
    include('content/js/partials/app.php');
    include('content/js/login.php')
  ?>

</body>

</html>