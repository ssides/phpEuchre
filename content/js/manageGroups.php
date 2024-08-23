<script type="text/javascript">

  function request(id, name) {
    this.id = id;
    this.name = name;
  }
  
  function manageGroupsViewModel(groupID) {
    var self = this;
    
    self.groupID = groupID;
    self.errorMessage = ko.observable('');
    self.selectedGroup = ko.observable();
    self.requests = ko.observableArray();

    self.getJoinRequests = function() {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.groupID = self.groupID;
      $.ajax({
        method: 'POST',
        url: 'api/getJoinRequests.php',
        data: pd,
        success: function (response) {
          let data = JSON.parse(response);
          let r = [];
          data.Requests.forEach(function (n) {
            r.push(new request(n[0], n[1]));
          });
          self.requests(r);
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

    self.serviceJoinRequest = function(code, playerID) {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.groupID = self.groupID;
      pd.code = code;
      pd.playerID = playerID;
      $.ajax({
        method: 'POST',
        url: 'api/serviceJoinRequest.php',
        data: pd,
        success: function (response) {
          self.getJoinRequests();
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

    self.serviceAccept = function() {
      self.serviceJoinRequest('A', this.id);
    };
    
    self.serviceDeny = function() {
      self.serviceJoinRequest('D', this.id);
    };

    self.setErrorMessage = function(e){
      self.errorMessage(e);
    };

    self.initialize = function() {
      self.getJoinRequests();
    };
    
    self.initialize();
  }
  
<?php if(!empty($$a['k']) && $isManager): ?>
  $(function () {

    var vm = new manageGroupsViewModel('<?php echo $$a['k']; ?>');
    ko.applyBindings(vm);
    
    var controllerError = '<?php echo str_replace("'", "\'", $controllerError); ?>';
    if (controllerError.length > 0) {
      vm.setErrorMessage(controllerError);
    }

  });
<?php endif; ?>

</script>
