<script type="text/javascript">

  function playerInfoViewModel() {
    var self = this;

    self.ix = 0;
    self.myPosition = ' ';
    self.thumbnailURL = ko.observable('');
    self.trumpURL = ko.observable('');
    self.name = ko.observable('');
    self.dealer = ko.observable(' ');
    self.pickedItUp = ko.observable(false);
    self.isPlayersTurn = ko.observable(false);
    self.isPlayerSkipped = ko.observable(false);
    self.trumpURL = ko.observable('');
    self.isLoner = ko.observable(false);
    
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
    
    self.lonerPosition = function(partner) {
      var p = '';
      switch(partner) {
        case 'O': p = 'P'; break;
        case 'P': p = 'O'; break;
        case 'L': p = 'R'; break;
        case 'R': p = 'L'; break;
      }
      return p;
    }
    
    self.update = function(gameData) { 
      if (self.myPosition == gameData.Dealer) {
        self.dealer('D');
      } else {
        self.dealer(' ');
      }

      self.isPlayersTurn(gameData.CardFaceUp[2] == 'U' ? (self.myPosition == gameData.Dealer ? true : false) : self.myPosition == gameData.Turn);
      self.isPlayerSkipped(gameData.CardFaceUp.length > 4 && self.myPosition == gameData.CardFaceUp[4]);
      var p = self.lonerPosition(gameData.CardFaceUp[4]);
      self.isLoner(gameData.CardFaceUp.length > 4 && self.myPosition == self.lonerPosition(gameData.CardFaceUp[4]));
      
      var trump = gameData.OrganizerTrump || gameData.OpponentTrump;
      
      if (trump && gameData.CardFaceUp.length > 3 && gameData.CardFaceUp[3] == self.myPosition) {
        self.pickedItUp(false);
        self.trumpURL(app.getCardURL(trump));
      } else if (!trump && gameData.CardFaceUp.length > 3 && gameData.CardFaceUp[2] == 'U' && gameData.CardFaceUp[3] == self.myPosition) {
        self.pickedItUp(true);
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