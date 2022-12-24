<script type="text/javascript">

  function playerInfoViewModel() {
    var self = this;

    self.ix = 0;
    self.myPosition = ' ';
    self.thumbnailURL = ko.observable('');
    self.trumpURL = ko.observable('');
    self.name = ko.observable('');
    self.dealer = ko.observable(' ');
    self.isPlayersTurn = ko.observable(false);
    self.isPlayerSkipped = ko.observable(false);
    self.trumpURL = ko.observable('');

    self.aSetThumbnailURL = [
      function(){},
      function(gameData){
        self.thumbnailURL(gameData.OThumbnailURL);
        self.name(gameData.OName);
      },
      function(gameData){
        self.thumbnailURL(gameData.PThumbnailURL);
        self.name(gameData.PName);
      },
      function(gameData){
        self.thumbnailURL(gameData.LThumbnailURL);
        self.name(gameData.LName);
      },
      function(gameData){
        self.thumbnailURL(gameData.RThumbnailURL);
        self.name(gameData.RName);
      }
    ];
    
    self.setThumbnailURL = function(gameData) { 
      self.aSetThumbnailURL[self.ix](gameData);
    };
    
    self.update = function(gameData) { 
      if (self.myPosition == gameData.Dealer) {
        self.dealer('D');
      } else {
        self.dealer(' ');
      }
      self.isPlayersTurn(self.myPosition == gameData.Turn);
      self.isPlayerSkipped(gameData.CardFaceUp.length > 4 && gameData.CardFaceUp[4] == self.myPosition);
      
      if ((gameData.OrganizerTrump || gameData.OpponentTrump) && gameData.CardFaceUp.length > 3 && gameData.CardFaceUp[3] == self.myPosition) {
        if (self.myPosition == 'O' || self.myPosition == 'P') {
          if (gameData.OrganizerTrump) {
            self.trumpURL(app.getCardURL(gameData.OrganizerTrump));
          }
        } else {
          if (gameData.OpponentTrump) {
            self.trumpURL(app.getCardURL(gameData.OpponentTrump));
          }
        }
      } else {
        self.trumpURL('');
      }
    };
    
    self.initialize = function(iam, selfPosition, gameData){
      if ((iam == 'N' && selfPosition == 'P')
          || (iam == 'E' && selfPosition == 'L')
          || (iam == 'W' && selfPosition == 'R')) {
        self.myPosition = 'O';
        self.ix = 1; // use Organizer data
      } else if ((iam == 'N' && selfPosition == 'O')
          || (iam == 'E' && selfPosition == 'R')
          || (iam == 'W' && selfPosition == 'L')) {
        self.myPosition = 'P';
        self.ix = 2; // use Partner data
      } else if ((iam == 'N' && selfPosition == 'R')
          || (iam == 'E' && selfPosition == 'P')
          || (iam == 'W' && selfPosition == 'O')) {
        self.myPosition = 'L';
        self.ix = 3; // use Left data
      } else if ((iam == 'N' && selfPosition == 'L')
          || (iam == 'E' && selfPosition == 'O')
          || (iam == 'W' && selfPosition == 'P')) {
        self.myPosition = 'R';
        self.ix = 4; // use Right data
      }
      
      self.setThumbnailURL(gameData);
    };
  }

</script>