<script type="text/javascript">

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
    
    self.update = function(myPosition, gameInfo) {
      self.submitted = false;
      self.showPassBtn(myPosition != gameInfo.Dealer);
      self.enablePassBtn(true);
      self.showSubmitBtn(true);
      self.enableSubmitBtn(true);
      self.myPosition = myPosition;
      self.alone(false);
      self.suits().forEach(function(s){
        s.isPlayable(s.id != gameInfo.CardFaceUp[1]);
        s.isSelected(false);
      });
    };
    
    // this is almost completely duplicated from currentPlayerInfoViewModel.  todo: only have one copy.
    self.setNextTurn = function() {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.myPosition;
      $.ajax({
        method: 'POST',
        url: 'api/setNextTurn.php',
        data: pd,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            } else {
              self.submitted = true;
            }
          } catch (error) {
            console.log('Could not parse response from setNextTurn. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };

    self.chooseTrump = function(trumpID, alone) {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.positionID = self.myPosition;
      pd.trumpID = trumpID;
      pd.alone = alone;
      
      $.ajax({
        method: 'POST',
        url: 'api/chooseTrump.php',
        data: pd,
        success: function(response) {
          try {
            let data = JSON.parse(response);
            if (data.ErrorMsg) {
              console.log(data.ErrorMsg);
            } else {
              self.submitted = true;
            }
          } catch (error) {
            console.log('Could not parse response from chooseTrump. ' + error + ': ' + response);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    };

    self.pass = function(){
      self.enablePassBtn(false);
      self.showSubmitBtn(false);
      self.setNextTurn();
    };
    
    self.getSelectedTrump = function(){
      var t = '';
      self.suits().forEach(function(s){
        if (s.isSelected()) {
          t = s.id;
        }
      });
      return t;
    };
    
    self.submit = function(){
      var trumpID = self.getSelectedTrump();
      if (trumpID) {
        self.enableSubmitBtn(false);
        self.showPassBtn(false);
        self.chooseTrump(trumpID, self.alone());
      }
    };
    
    self.selectSuit = function(suit) {
      self.suits().forEach(function(s){
        if (s.id == suit.id && s.isPlayable()) {
          s.isSelected(!s.isSelected());
        } else {
          s.isSelected(false);
        }
      });
    };
    
    self.getSuitObject = function(s) {
      var o = {
        id: s, 
        url: app.getCardURL(s),
        isPlayable: ko.observable(true),
        isSelected: ko.observable(false)
      };
      return o;
    };
    
    self.isActive = function(iamActive) {
      self.iamActive = iamActive;
    };
    
    self.hotKeys = function() {
      if (self.iamActive) {
        if (event.code == 'Space' && event.ctrlKey === false && event.shiftKey === false) {
          self.alone(!self.alone());
        } else if (event.code == 'KeyS' && event.ctrlKey === false) {
          self.selectSuit({ id: 'S' });
        } else if (event.code == 'KeyC' && event.ctrlKey === false) {
          self.selectSuit({ id: 'C' });
        } else if (event.code == 'KeyH' && event.ctrlKey === false) {
          self.selectSuit({ id: 'H' });
        } else if (event.code == 'KeyD' && event.ctrlKey === false) {
          self.selectSuit({ id: 'D' });
        } else if (event.code == 'KeyX' && event.ctrlKey === false) {
          if (self.showPassBtn()) {
            $('#bidmodalpass').click();
          }
        } else if ((event.code == 'Enter' && event.ctrlKey === false && event.shiftKey === false)
          || (event.code == 'NumpadEnter' && event.ctrlKey === false && event.shiftKey === false)) {
          $('#bidmodalsubmit').click();
        } 
      }
    };
    

    self.initialize = function() {
      self.submitted = false;
      var s = [];
      s.push(self.getSuitObject('D'));
      s.push(self.getSuitObject('C'));
      s.push(self.getSuitObject('H'));
      s.push(self.getSuitObject('S'));
      self.suits(s);
      
      document.addEventListener('keyup', self.hotKeys);
    }
    
    self.initialize();
  }
  
</script>