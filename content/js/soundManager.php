<?php include_once('../../config/config.php'); ?>

<script type="text/javascript">

  function playSound() {
      var audio = new Audio('<?php echo $appUrl.$audioDir."loaner.mp3"; ?>');
      audio.play();
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

    var loginError = '<?php echo str_replace("'", "\'", $loginError); ?>';
    if (loginError.length > 0) {
      vm.setErrorMessage(loginError);
    }

  });
</script>
