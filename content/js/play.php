<?php include_once('../../config/config.php'); ?>

<script type="text/javascript">

  function playViewModel() {
    var self = this;
    
    self.initialize = function() {
    }
    
    self.initialize();
  }    
  
  $(function () {
    ko.applyBindings(new playViewModel());
  });
  
</script>
