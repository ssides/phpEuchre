<script type="text/javascript">
  
  function scoreViewModel() {
    var self = this;
    
    self.ix = 0;
    self.label = ko.observable('');
    self.score = ko.observable(0);
    self.tricks = ko.observable('');
    self.showScoreGroup = ko.observable(false);
    
    self.getTricks = function(t){
      var tricks = '';
      for(let i = 0; i < t; i++) {
        tricks += '&#149;&nbsp;';
      }
      return tricks;
    };
    self.aUpdate = [
      function(){ },
      function(gameData){
        if (gameData.OrganizerTricks) {
          self.tricks(self.getTricks(gameData.OrganizerTricks));
        } else {
          self.tricks('&nbsp;');
        }
        self.score(gameData.OrganizerScore);
      },
      function(gameData){
        if (gameData.OpponentTricks) {
          self.tricks(self.getTricks(gameData.OpponentTricks));
        } else {
          self.tricks('&nbsp;');
        }
        self.score(gameData.OpponentScore);
      },
    ];
    
    self.update = function(gameData){
      self.aUpdate[self.ix](gameData);
      self.showScoreGroup(true);
    };
    
    self.initialize = function(iam, selfPosition, gameData){
      if (iam == 'M' && (selfPosition == 'O' || selfPosition == 'P')) {
        self.label('Us');
        self.ix = 1;  // use Organizer data
      } else if (iam == 'M' && (selfPosition == 'L' || selfPosition == 'R')) {
        self.label('Us');
        self.ix = 2;  // use Opponent data
      } else if (iam == 'O' && (selfPosition == 'O' || selfPosition == 'P')) {
        self.label('Them');
        self.ix = 2;  // use Opponent data
      } else if (iam == 'O' && (selfPosition == 'L' || selfPosition == 'R')) {
        self.label('Them');
        self.ix = 1;  // use Organizer data
      }
    };
  }
  
</script>