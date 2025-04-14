<script type="text/javascript">

  function playSound() {
      app.soundQueue.push(app.sounds["yourturn"]);
      app.soundQueue.push(app.sounds["C"]);
      app.soundQueue.push(app.sounds["DKC"]);
  }

  function soundManagerViewModel() {
    var self = this;
    
    self.errorMessage = ko.observable('');
    
    self.setErrorMessage = function(e){
      self.errorMessage(e);
    };
  }
  
  $(function () {
    var vm = new soundManagerViewModel();
    ko.applyBindings(vm);
    
    setInterval(app.soundPop, 300);

    var loginError = '<?php echo str_replace("'", "\'", $loginError); ?>';
    if (loginError.length > 0) {
      vm.setErrorMessage(loginError);
    }

  });
</script>
