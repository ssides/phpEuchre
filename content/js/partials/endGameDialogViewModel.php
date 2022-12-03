<script type="text/javascript">

  function endGameDialogViewModel() {
    var self = this;
    
    self.winner = ko.observable();
    self.loser = ko.observable();
    self.dateStr = ko.observable();
    
    self.ok = function(){
      window.location.href = app.appURL + 'dashboard.php';
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
                self.winner(orgMsg);
                self.loser(oppMsg);
              } else {
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
    
    self.initialize = function() {
      self.getWinner();
    }
    
    self.initialize();
  }
  
</script>