<script type="text/javascript">

  function gameError(message) {
    this.message = message;
  }

  function errorViewModel() {
    var self = this;

    self.errors = ko.observableArray([]);
    self.errorsCount = ko.computed(function() {
      return self.errors().length;
    });
    
    self.add = function(message) {
      self.errors.push(new gameError(message));
    };
    
    self.clear = function() {
      self.errors([]);
    };
    
  }

</script>