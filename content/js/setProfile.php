<script type="text/javascript">

  function setProfileViewModel() {
    var self = this;
    
    self.errorMessage = ko.observable('');
    self.thumbnailurl = ko.observable('');
    self.pageReady = ko.observable(false);
    
    self.setErrorMessage = function(e){
      self.errorMessage(e);
    };

    self.getThumbnailURL = function(){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      $.ajax({
        method: 'POST',
        url: 'api/profile/getThumbnailURL.php',
        data: pd,
        success: function (response) {
          let data = JSON.parse(response);
          self.thumbnailurl(data.ThumbnailURL);
          self.pageReady(true);
        },
        error: function (xhr, status, error) {
          self.errorMessage(error);
        }
      });
    };
    
    self.up = function(){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      $.ajax({
        method: 'POST',
        url: 'api/profile/up.php',
        data: pd,
        success: function (response) {
          self.getThumbnailURL();
        },
        error: function (xhr, status, error) {
          self.errorMessage(error);
        }
      });
    }
    
    self.down = function(){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      $.ajax({
        method: 'POST',
        url: 'api/profile/down.php',
        data: pd,
        success: function (response) {
          self.getThumbnailURL();
        },
        error: function (xhr, status, error) {
          self.errorMessage(error);
        }
      });
    }
    
    self.left = function(){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      $.ajax({
        method: 'POST',
        url: 'api/profile/left.php',
        data: pd,
        success: function (response) {
          self.getThumbnailURL();
        },
        error: function (xhr, status, error) {
          self.errorMessage(error);
        }
      });
    }
    
    self.right = function(){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      $.ajax({
        method: 'POST',
        url: 'api/profile/right.php',
        data: pd,
        success: function (response) {
          self.getThumbnailURL();
        },
        error: function (xhr, status, error) {
          self.errorMessage(error);
        }
      });
    }
    
    self.zoomin = function(){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      $.ajax({
        method: 'POST',
        url: 'api/profile/zoomin.php',
        data: pd,
        success: function (response) {
          self.getThumbnailURL();
        },
        error: function (xhr, status, error) {
          self.errorMessage(error);
        }
      });
    }
    
    self.zoomout = function(){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      $.ajax({
        method: 'POST',
        url: 'api/profile/zoomout.php',
        data: pd,
        success: function (response) {
          self.getThumbnailURL();
        },
        error: function (xhr, status, error) {
          self.errorMessage(error);
        }
      });
    }
    
    self.change = function(){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      $.ajax({
        method: 'POST',
        url: 'api/profile/change.php',
        data: pd,
        success: function (response) {
          self.thumbnailurl('');
        },
        error: function (xhr, status, error) {
          self.errorMessage(error);
        }
      });
    }
    
    self.initialize = function(){
      self.getThumbnailURL();
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
