
  function bidDialogViewModel() {
    var self = this;
    
    self.submitted = false;
    self.myPosition = '';
    self.showPassBtn = ko.observable(true);
    self.enablePassBtn = ko.observable(true);
    self.showSubmitBtn = ko.observable(true);
    self.enableSubmitBtn = ko.observable(true);
    self.suits = ko.observableArray();
    self.alone = ko.observable();
    
    self.selectSuit = function(){};
    self.pass = function(){};
    self.submit = function(){};
  }
  
