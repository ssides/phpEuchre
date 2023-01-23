<?php include_once('../../config/config.php'); ?>

<script type="text/javascript">
  
  function player(name, id, thumbnailpath) {
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

  function organizeViewModel() {
    var self = this;
    
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
    self.allPlayers = ko.computed(function () {
      if ((self.selectedLeft() !== undefined)
        && (self.selectedRight() !== undefined)
        && (self.selectedPartner() !== undefined)) {
        return unique(self.selectedLeft(), self.selectedRight(), self.selectedPartner());
      } else {
        return false;
      };
    });
    self.allPlayersJoined = ko.computed(function(){
      return self.leftJoined() && self.rightJoined() && self.partnerJoined();
    });
    
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
      var postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'"
                            .",gameID:'{$_SESSION['gameID']}'"   ?>
                ,identifier: 'partner' 
                ,player: this.selectedPartner().id};
      self.invitePlayer(postData);
    };
    
    self.inviteLeft = function() {
      var postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'"
                            .",gameID:'{$_SESSION['gameID']}'"   ?>
                ,identifier: 'left' 
                ,player: this.selectedLeft().id};
      self.invitePlayer(postData);
    };
    
    self.inviteRight = function() {
      var postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'"
                            .",gameID:'{$_SESSION['gameID']}'"   ?>
                ,identifier: 'right' 
                ,player: this.selectedRight().id};
      self.invitePlayer(postData);
    };
    
    self.getRSVPs = function() {
      if (!self.getRSVPSInProgress){
        self.getRSVPSInProgress = true;
        var postData = { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'"
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
    
    self.initialize = function() {
      $.ajax({
        method: 'POST',
        url: 'api/getUsers.php',
        data: { <?php echo $cookieName.':'."'{$_COOKIE[$cookieName]}'"?> },
        success: function (response) {
          let data = JSON.parse(response);
          if (data.ErrorMsg) {
            console.log(data.ErrorMsg);
          }
          data.Users.forEach(function (i) {
            self.users.push(new player(i[1], i[0], i[2]));
          });
        },
        error: function (xhr, status, error) {
          console.log(error);
        }
      });
      
      self.getRSVPSTimer = setInterval(self.getRSVPs, 1000);
    };
    
    self.initialize();
  }

  $(function () {
    
    ko.applyBindings(new organizeViewModel());
    
  });
</script>
