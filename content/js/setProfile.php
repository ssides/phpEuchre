<script type="text/javascript">

  function setProfileViewModel() {
    var self = this;
    
    self.errorMessage = ko.observable('');
    
    self.setErrorMessage = function(e){
      self.errorMessage(e);
    };

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
