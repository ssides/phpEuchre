
  function playerInfoViewModel() {
    var self = this;

    self.ix = 0;
    self.myPosition = ' ';
    self.thumbnailURL = ko.observable('');
    self.name = ko.observable('');
    self.dealer = ko.observable(' ');
    self.infoStatus = ko.observable('infoBorder');
  }

