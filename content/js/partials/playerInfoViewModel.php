<script type="text/javascript">

  function playerInfoViewModel() {
    var self = this;

    self.ix = 0;
    self.myPosition = ' ';
    self.thumbnailURL = ko.observable('');
    self.name = ko.observable('');
    self.dealer = ko.observable(' ');
    self.infoStatus = ko.observable('infoBorder');
    self.aSetURL = [
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
    self.setURL = function(gameData) { 
      self.aSetURL[self.ix](gameData) 
    };
    self.update = function(gameData) { 
      if (self.myPosition == gameData.Dealer) {
        self.dealer('D');
      } else {
        self.dealer(' ');
      }
      if (self.myPosition == gameData.Turn) {
        self.infoStatus('infoTurnBorder');
      } else {
        self.infoStatus('infoBorder');
      }
    };
    
    // When I started working on bidding, I changed this view model.
    // It's no longer used for South, so there is no need to test for 
    // that here.  Having that check was nice for getting things going.
    self.initialize = function(iam, selfPosition, gameData){
      if ((iam == 'N' && selfPosition == 'P')
          || (iam == 'S' && selfPosition == 'O')
          || (iam == 'E' && selfPosition == 'L')
          || (iam == 'W' && selfPosition == 'R')) {
        self.myPosition = 'O';
        self.ix = 1; // use Organizer data
      } else if ((iam == 'N' && selfPosition == 'O')
          || (iam == 'S' && selfPosition == 'P')
          || (iam == 'E' && selfPosition == 'R')
          || (iam == 'W' && selfPosition == 'L')) {
        self.myPosition = 'P';
        self.ix = 2; // use Partner data
      } else if ((iam == 'N' && selfPosition == 'R')
          || (iam == 'S' && selfPosition == 'L')
          || (iam == 'E' && selfPosition == 'P')
          || (iam == 'W' && selfPosition == 'O')) {
        self.myPosition = 'L';
        self.ix = 3; // use Left data
      } else if ((iam == 'N' && selfPosition == 'L')
          || (iam == 'S' && selfPosition == 'R')
          || (iam == 'E' && selfPosition == 'O')
          || (iam == 'W' && selfPosition == 'P')) {
        self.myPosition = 'R';
        self.ix = 4; // use Right data
      }
      
      self.setURL(gameData);
    };
  }

</script>