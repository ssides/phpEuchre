<script type="text/javascript">

  function endGameDialogViewModel() {
    var self = this;
    
    self.winner = ko.observable();
    self.loser = ko.observable();
    
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
            debugger;
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            } else {
              
              if (Number(data.OrganizerScore) > Number(data.OpponentScore)) {
                self.winner(data.OName + ' & ' + data.PName + ': ' + Number(data.OrganizerScore).toString());
                self.loser(data.LName + ' & ' + data.RName + ': ' + Number(data.OpponentScore).toString());
              } else {
                self.winner(data.LName + ' & ' + data.RName + ': ' + Number(data.OpponentScore).toString());
                self.loser(data.OName + ' & ' + data.PName + ': ' + Number(data.OrganizerScore).toString());
              }
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
    
    self.initialize = function() {
      self.getWinner();
    }
    
    self.initialize();
  }
  
</script>