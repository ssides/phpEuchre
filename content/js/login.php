<?php include_once('../../config/config.php'); ?>

<script type="text/javascript">

  function group(id, description) {
    this.id = id;
    this.description = description;
  }

  function loginViewModel() {
    var self = this;
    
    self.errorMessage = ko.observable('');
    self.name = ko.observable('');
    self.password = ko.observable('');
    self.selectedGroup = ko.observable();
    self.groups = ko.observableArray();
    
    self.getGroups = function() {
      $.ajax({
        method: 'POST',
        url: 'api/groups/getGroups.php',
        data: { },
        success: function (response) {
          let data = JSON.parse(response);
          if (data.ErrorMsg) {
            console.log(data.ErrorMsg);
          }
          let g = [];
          data.Groups.forEach(function (i) {
            g.push(new group(i[0], i[1]));
          });
          self.groups(g);
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          console.log(error);
        },
        complete: function(){
          self.invitationInProgress = false;
        }
      });
    };

    self.setErrorMessage = function(e){
      self.errorMessage(e);
    };
    
    self.validatePage = function(){
      var msg = '';
      if (self.name().length == 0) {
        msg = 'Please enter a name.';
      } else if (self.password().length == 0) {
        msg = 'Please enter a password.';
      } 
      return msg;
    };
    
    self.validateSubmit = function() {
      var v = self.validatePage();
      if (v.length == 0) {
        $('#loginForm').submit();
      } else {
        self.errorMessage(v);
      }
    };
    
    self.initialize = function() {
     self.getGroups();
    };
    
    self.initialize();
  }    
  
  $(function () {
    var vm = new loginViewModel();
    ko.applyBindings(vm);
    
    var loginError = '<?php echo str_replace("'", "\'", $loginError); ?>';
    if (loginError.length > 0) {
      vm.setErrorMessage(loginError);
    }
    
  });
</script>
