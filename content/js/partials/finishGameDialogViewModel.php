<script type="text/javascript">

  function finishGameDialogViewModel() {
    var self = this;
    
    self.winner = ko.observable();
    self.loser = ko.observable();
    self.dateStr = ko.observable();
    self.position = '';
    
    self.ok = function(){
      window.location.href = 'dashboard.php';
    };
    
    self.getWinner = function(){
      $.ajax({
        method: 'POST',
        url: 'api/getWinner.php',
        data: app.apiPostData,
        success: function (response) {
          try {
            var data = JSON.parse(response);

            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            } else {
              var orgScore = parseInt(data.OrganizerScore);
              var oppScore = parseInt(data.OpponentScore);
              var orgMsg = data.OName + ' & ' + data.PName + ': ' + data.OrganizerScore;
              var oppMsg = data.LName + ' & ' + data.RName + ': ' + data.OpponentScore;
              
              if (orgScore > oppScore) {
                if (self.position == 'O' || self.position == 'P') {
                  app.soundQueue.push(app.sounds["gamewinner"]);
                } else {
                  app.soundQueue.push(app.sounds["gameloser"]);
                }
                self.winner(orgMsg);
                self.loser(oppMsg);
              } else {
                if (self.position == 'L' || self.position == 'R') {
                  app.soundQueue.push(app.sounds["gamewinner"]);
                } else {
                  app.soundQueue.push(app.sounds["gameloser"]);
                }

                self.winner(oppMsg);
                self.loser(orgMsg);
              }
              
              self.dateStr(new Date().toLocaleString("en-US", { month: 'long', day: 'numeric', year: 'numeric', hour12: true, hour:'numeric', minute: 'numeric' }));
            }
          } catch (error) {
            console.log('Error ' + ': ' + error.message || error);
            console.log(error.stack);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };
    
    self.update = function() {
      self.getWinner();
    };
    
    self.initialize = function(selfPosition) {
      self.position = selfPosition;
    }
    
    self.initialize();
  }
  
</script>