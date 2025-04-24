<script type="text/javascript">
  
  function manageGroupsViewModel() {
    var self = this;
    
    self.createGroupMessage = ko.observable('');
    self.groupName = ko.observable('');
    self.selectedGroup = ko.observable();
    self.requests = ko.observableArray();
    self.memberOf = ko.observableArray();
    self.notMemberOf = ko.observableArray();
    self.enableCreateGroupBtn = ko.observable(true);
    self.isCreateGroupError = ko.observable(false);
    self.requestSentMessage = ko.observable('');
    self.isRequestSentError = ko.observable(false);
    self.isJoinRequestError = ko.observable(false);
    self.joinRequestMessage = ko.observable('');
    self.isGroupMemberError = ko.observable(false);
    self.groupMemberMessage = ko.observable('');
    
    self.getJoinRequests = function() {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      $.ajax({
        method: 'POST',
        url: 'api/getJoinRequests.php',
        data: pd,
        success: function (response) {
          let data = JSON.parse(response);
          let r = [];
          data.Requests.forEach(function (n) {
            r.push(new request(n.ID, n.GroupID, n.Description, n.PlayerID, n.Name));
          });
          self.requests(r);
        },
        error: function (xhr, status, error) {
          self.isJoinRequestError(true);
          self.joinRequestMessage(error);
        }
      });
    };

    self.serviceJoinRequest = function(code, groupID, playerID) {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.groupID = groupID;
      pd.code = code;
      pd.playerID = playerID;
      $.ajax({
        method: 'POST',
        url: 'api/groups/serviceJoinRequest.php',
        data: pd,
        success: function (response) {
          self.isJoinRequestError(false);
          self.joinRequestMessage(code == 'A' ? 'Accepted' : 'Denied');
        },
        error: function (xhr, status, error) {
          self.isJoinRequestError(true);
          self.joinRequestMessage(error);
        },
        complete: function(){
          self.invitationInProgress = false;
        }
      });
    };

    self.serviceAccept = function() {
      self.serviceJoinRequest('A', this.groupID, this.playerID);
    };
    
    self.serviceDeny = function() {
      self.serviceJoinRequest('D', this.groupID, this.playerID);
    };

    self.sendJoinRequest = function() {
      self.isCreateGroupError(false);
      self.requestSentMessage('');
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.groupID = this.groupID;
      $.ajax({
        method: 'POST',
        url: 'api/groups/sendJoinRequest.php',
        data: pd,
        success: function (response) {
          let data = JSON.parse(response);
          if (data.ErrorMsg) {
            self.isRequestSentError(true);
            self.requestSentMessage(data.ErrorMsg);
          } else {
            self.isRequestSentError(false);
            self.requestSentMessage("Your request has been sent.");
          }
        },
        error: function(xhr, status, error) {
          self.isCreateGroupError(true);
          self.requestSentMessage(error);
        }
      });
    }
    
    self.getGroupsIBelongTo = function(){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      $.ajax({
        method: 'POST',
        url: 'api/groups/getGroupsIBelongTo.php',
        data: pd,
        success: function(response) {
          let data = JSON.parse(response);
          let r = [];
          data.Groups.forEach(function (n) {
            r.push(new groupMemberOf(n.Description, n.IsManager));
          });
          self.memberOf(r);
        },
        error: function(xhr, status, error){
          self.isCreateGroupError(true);
          self.createGroupMessage(error);
        }
      });
    };

    self.getGroupsIMightWantToJoin = function(){
      var pd = {};
      Object.assign(pd, app.apiPostData);
      $.ajax({
        method: 'POST',
        url: 'api/groups/getGroupsIMightWantToJoin.php',
        data: pd,
        success: function(response) {
          let data = JSON.parse(response);
          let r = [];
          data.Groups.forEach(function (n) {
            r.push(new groupNotAMemberOf(n.ID, n.Description));
          });
          self.notMemberOf(r);
        },
        error: function(xhr, status, error){
          self.isRequestSentError(true);
          self.requestSentMessage(xhr.responseText);
        }
      });
    };

    self.createGroup = function() {
      var pd = {};
      Object.assign(pd, app.apiPostData);
      pd.groupName = self.groupName();
      $.ajax({
        method: 'POST',
        url: 'api/groups/createGroup.php',
        data: pd,
        success: function(response) {
          let data = JSON.parse(response);
          if (data.ErrorMsg) {
            self.isCreateGroupError(true);
            self.createGroupMessage(data.ErrorMsg);
          } else {
            self.createGroupMessage("Created group '" + self.groupName() + "' you are the manager.");
          }
        },
        error: function(xhr, status, error) {
          self.isCreateGroupError(true);
          self.createGroupMessage(xhr.responseText);
        },
        complete: function() { 
          self.enableCreateGroupBtn(true); 
        }
      });
    }
    
    self.createGroupBtnClick = function(){ 
      self.enableCreateGroupBtn(false);
      self.isCreateGroupError(false);
      self.createGroup();
    };

    self.refreshPage = function() {
      self.getGroupsIMightWantToJoin();
      self.getGroupsIBelongTo();
      self.getJoinRequests();
    };

    self.initialize = function() {
      self.refreshPage();
      setInterval(self.refreshPage, 2000);
    };
    
    self.initialize();
  }
  
  $(function () {
    var vm = new manageGroupsViewModel();
    ko.applyBindings(vm);
  });

</script>
