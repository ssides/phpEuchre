
  function endGameDialogViewModel() {
    var self = this;
    
    self.winner = ko.observable();
    self.loser = ko.observable();
    self.dateStr = ko.observable();
    
    self.ok = function(){
    };
    
    self.initialize = function() {
      var orgMsg = 'Barb & Steve: 11';
      var oppMsg = 'Judy & Chris: 8';

      self.winner(orgMsg);
      self.loser(oppMsg);
    }
    
    self.initialize();
  }
  
