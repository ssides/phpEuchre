<?php include_once('../../config/config.php'); ?>

<script type="text/javascript">
  
  function player(id, name, thumbnailpath) {
    this.name = name;
    this.id = id;
    this.thumbnailpath = thumbnailpath;
  }

  function unique(left, right, partner) {
    const u = [];
    u.push(left.name);
    if (u.includes(right.name)) {
      return false;
    } else {
      u.push(right.name);
      return !u.includes(partner.name);
    }
  };

  function organizeViewModel(groupID) {
    var self = this;
    
    self.groupID = groupID;
    self.getRSVPSTimer = null;
    self.getRSVPSInProgress = false;   // lets the $.ajax() call take more than a second to complete.
    self.users = ko.observableArray();
    self.selectedLeft = ko.observable();
    self.leftInvited = ko.observable(false);
    self.leftJoined = ko.observable(false);
    self.selectedRight = ko.observable();
    self.rightInvited = ko.observable(false);
    self.rightJoined = ko.observable(false);
    self.selectedPartner = ko.observable();
    self.partnerInvited = ko.observable(false);
    self.partnerJoined = ko.observable(false);
    self.playTo = ko.observable('10');
    self.gameSpeed = ko.observable('0');
    self.errorMessage = ko.observable('');
    self.allPlayers = ko.computed(function() {
      if ((self.selectedLeft() !== undefined)
        && (self.selectedRight() !== undefined)
        && (self.selectedPartner() !== undefined)) {
        return unique(self.selectedLeft(), self.selectedRight(), self.selectedPartner());
      } else {
        return false;
      };
    });
    self.displayGameSpeed = ko.computed(function() {
      if (self.gameSpeed() == '1') {
        return 'fast';
      } else {
        return 'slow';
      }
    });
    self.allPlayersJoined = ko.computed(function(){
      return self.leftJoined() && self.rightJoined() && self.partnerJoined();
    });

    self.toggleSpeed = function(){
      if (self.gameSpeed() == '1') {
        self.gameSpeed('0');
      } else {
        self.gameSpeed('1');
      }
    };
    
    self.setInvited = function(identifier) {
      if (identifier == 'partner') {
        self.partnerInvited(true);
      } else if (identifier =='left') {
        self.leftInvited(true);
      } else {
        self.rightInvited(true);
      }
    };
    
    self.invitePlayer = function(postData) {
      $.ajax({
        method: 'POST',
        url: 'api/invitePlayer.php',
        data: postData,
        success: function (response) {
          if (response == 'OK') {
            self.setInvited(postData.identifier);
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          console.log(error);
        }
      });
    };

    self.invitePartner = function() {
      var postData = { <?php echo 'r:'."'{$$a['r']}'"
                            .",gameID:'{$_SESSION['gameID']}'"   ?>
                ,identifier: 'partner' 
                ,player: this.selectedPartner().id};
      self.invitePlayer(postData);
    };
    
    self.inviteLeft = function() {
      var postData = { <?php echo 'r:'."'{$$a['r']}'"
                            .",gameID:'{$_SESSION['gameID']}'"   ?>
                ,identifier: 'left' 
                ,player: this.selectedLeft().id};
      self.invitePlayer(postData);
    };
    
    self.inviteRight = function() {
      var postData = { <?php echo 'r:'."'{$$a['r']}'"
                            .",gameID:'{$_SESSION['gameID']}'"   ?>
                ,identifier: 'right' 
                ,player: this.selectedRight().id};
      self.invitePlayer(postData);
    };
    
    self.getRSVPs = function() {
      if (!self.getRSVPSInProgress){
        self.getRSVPSInProgress = true;
        var postData = { <?php echo 'r:'."'{$$a['r']}'"
                      .",gameID:'{$_SESSION['gameID']}'"   ?> };
        self.getRSVPSInProgress = $.ajax({
          method: 'POST',
          url: 'api/getRSVPs.php',
          data: postData,
          success: function (response) {
            let data = JSON.parse(response);
            if (data.LeftJoinDate)
              self.leftJoined(true);
            else 
              self.leftJoined(false);
            if (data.RightJoinDate)
              self.rightJoined(true);
            else 
              self.rightJoined(false);
            if (data.PartnerJoinDate)
              self.partnerJoined(true);
            else 
              self.partnerJoined(false);
          },
          error: function (xhr, status, error) {
            console.log(xhr.responseText);
            console.log(error);
          },
          complete: function(){
            self.getRSVPSInProgress = false;
          }
        });
      }
    };
    
    self.inviteAll = function() {
      if (!self.leftInvited()) {
        self.inviteLeft();
      }    
      if (!self.rightInvited()) {
        self.inviteRight();
      }    
      if (!self.partnerInvited()) {
        self.invitePartner();
      }    
    };
    
    self.getUsers = function() {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.groupID = self.groupID;
      $.ajax({
        method: 'POST',
        url: 'api/getUsers.php',
        data: pd,
        success: function (response) {
          let data = JSON.parse(response);
          if (data.ErrorMsg) {
            console.log(data.ErrorMsg);
          }
          data.Users.forEach(function (i) {
            self.users.push(new player(i[0], i[1], i[2]));
          });
        },
        error: function (xhr, status, error) {
          console.log(error);
        }
      });
    };
    
    self.setErrorMessage = function(e){
      self.errorMessage(e);
    };

    self.initialize = function() {
      self.getUsers();
      self.getRSVPSTimer = setInterval(self.getRSVPs, 1000);
    };
    
    self.initialize();
  }
  
<?php if(!empty($$a['k'])): ?>
  $(function () {
    var vm = new organizeViewModel('<?php echo $$a['k']; ?>');
    ko.applyBindings(vm);

    var controllerError = '<?php echo str_replace("'", "\'", $controllerError); ?>';
    if (controllerError.length > 0) {
      vm.setErrorMessage(controllerError);
    }
  });
  

<?php endif; ?>

  
</script>
