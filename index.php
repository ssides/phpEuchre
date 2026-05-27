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
  <?php include('header.php'); ?>

  <div class="App">
    <div class="vertical-center">
      <div class="inner-block">
        <form id="loginForm" action="" method="post">
          <h3>Hello</h3>

          <?php if(isset($_SESSION['login_error'])) {
            echo('<div class="alert alert-danger">');
            echo($_SESSION['login_error']);
            echo('</div>');
            } 
          ?>

          <div class="alert alert-danger" style="display:none" data-bind="visible: errorMessage().length > 0" >
              <div data-bind="text: errorMessage"></div>
          </div>
          <table>
            <tr>
              <td class="labelCell"><label for="name_signin">Name</label></td>
              <td>
                  <div class="position-relative">
                      <input type="text" 
                             name="name_signin" 
                             id="name_signin" 
                             class="form-control"
                             data-bind="value: name, 
                                        valueUpdate: 'input',
                                        event: { 
                                            input: searchNames, 
                                            keydown: handleKeyDown,
                                            blur: function(){ setTimeout(function(){ $root.showSuggestions(false); }, 200); }
                                        }" 
                             autocomplete="off" />
                      
                      <!-- Suggestions dropdown -->
                      <div class="list-group position-absolute w-100" 
                           data-bind="visible: showSuggestions" 
                           style="max-height: 300px; overflow-y: auto; z-index: 1000; margin-top: 2px; border: 1px solid #ccc;">
                          <!-- ko foreach: suggestions -->
                          <a href="#" class="list-group-item list-group-item-action" 
                             data-bind="text: $data, 
                                        css: { active: $index() === $parent.highlightIndex() },
                                        click: function(){ $parent.selectSuggestion($data); }">
                          </a>
                          <!-- /ko -->
                      </div>
                  </div>
                  </br>
              </td>
            </tr>
            <tr>
              <td><label for="selectgroup">Group&nbsp;(optional)&nbsp;</label></td>
              <td class="sfeTooltip">
                <select id="selectgroup" data-bind="options: groups, optionsText: 'description', value: selectedGroup, optionsCaption:'Select'"></select>
                <input type="hidden" data-bind="value: selectedGroup() ? selectedGroup().description : ''" id="group_signin" name="group_signin" />
                <input type="hidden" data-bind="value: selectedGroup() ? selectedGroup().id : ''" id="group_id" name="group_id" />
                <span class="sfeTooltiptext">Selecting a group will give you the option of starting a game.</span>
              </td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><button type="submit" name="login" id="sign_in" class="btn btn-outline-primary btn-lg btn-block" data-bind="click: validateSubmit">Sign in</button>
              </td>
            </tr>
          </table>
        </form>
      </div>
    </div>
  </div>
  
  <?php
    include('content/js/login.php')
  ?>

</body>

</html>