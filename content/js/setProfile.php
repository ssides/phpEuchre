<script type="text/javascript">

  function group(id, description) {
    this.id = id;
    this.description = description;
  }

  function setProfileViewModel() {
    var self = this;
    
    self.errorMessage = ko.observable('');
    self.selectedGroup = ko.observable();
    self.groups = ko.observableArray();
    
    // Get groups I'm not a member of and have no pending requests to join 
    // or the pending requests were all declined.
    self.getNewGroups = function() {
      $.ajax({
        method: 'POST',
        url: 'api/getNewGroups.php',
        data: app.apiPostData,
        success: function (response) {
          let data = JSON.parse(response);
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

    self.initialize = function() {
      self.getNewGroups();
    };
    
    self.initialize();
  }    
  
  $(function () {
    var vm = new setProfileViewModel();
    ko.applyBindings(vm);
    
    var controllerError = '<?php echo str_replace("'", "\'", $controllerError); ?>';
    if (controllerError.length > 0) {
      vm.setErrorMessage(controllerError);
    }

  });

</script>
