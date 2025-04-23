<?php
  include_once('config/db.php');
  include_once('config/config.php');
  require('authorize.php');
  include_once('svc/group.php');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="./content/bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo './content/css/site.css?v='.$version ?>">
  <title>Sides Family Euchre - Manage Groups</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="./content/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="./content/ko/knockout-3.5.1.js"></script>
</head>

<body>

  <?php include('header.php'); ?>
  <div class="App">
    <div class="vertical-center">
      <div class="inner-block manageGroupBlock">
        <table class="groupTable">
          <tr>
            <td class="groupCell">
              <div class="row groupRowPadding">
                <div class="col-12">
                  <div class="groupTitle">Groups I Am A Member Of:</div>
                </div>
              </div>
              <div class="row groupRowPadding">
                <div class="col-12">
                  <table style="width: 100%" data-bind="visible: memberOf().length > 0">
                    <tbody data-bind="foreach: memberOf">
                      <tr>
                        <td class="groupName">
                          <span data-bind="text: description"></span><span data-bind="visible: isManager"> (Manager)</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <div style="display: none" data-bind="visible: memberOf().length == 0">
                    <span class="groupName">You are not a member of any group.</span>
                  </div>
                </div>
              </div>
                <div class="row">
                  <div class="col-12">
                    <div class="alert groupMessage" style="display:none" data-bind="visible: groupMemberMessage().length > 0, class: isGroupMemberError() ? 'alert-danger' : 'alert-secondary'">
                      <div data-bind="text: groupMemberMessage"></div>
                    </div>
                  </div>
                </div>
            </td>
          </tr>
          <tr>
            <td class="groupCell">
              <div class="row groupRowPadding">
                <div class="col-12">
                  <div class="groupTitle">Groups I Might Want To Become A Member Of:</div>
                </div>
              </div>
              <div class="row groupRowPadding">
                <div class="col-12">
                  <table style="width: 100%" data-bind="visible: notMemberOf().length > 0">
                    <tbody data-bind="foreach: notMemberOf">
                      <tr>
                        <td>
                          <span class="groupName" data-bind="text: description"></span>
                        </td>
                        <td>
                          <button class="btn btn-outline-primary btn-sm" data-bind="click: $parent.sendJoinRequest, attr: { id: 'req' + groupID }">Send a Join Request</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <div style="display: none" data-bind="visible: notMemberOf().length == 0">
                    <span class="groupName">There are no groups to join.</span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="alert groupMessage" style="display:none" data-bind="visible: requestSentMessage().length > 0, class: isRequestSentError() ? 'alert-danger' : 'alert-secondary'">
                    <div data-bind="text: requestSentMessage"></div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <td class="groupCell">
              <div class="row groupRowPadding">
                <div class="col-12">
                  <div class="groupTitle">Create a Group:</div>
                </div>
              </div>
              <div class="row groupRowPadding">
                <div class="col"><input type="text" name="group_name" id="group_name" data-bind="value: groupName" /></div>
                <div class="col"><button id="createGroupBtn" type="button" class="buttonSize" data-bind="enable: enableCreateGroupBtn, click: createGroupBtnClick">Create and Manage</button></div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="alert groupMessage" style="display:none" data-bind="visible: createGroupMessage().length > 0, class: isCreateGroupError() ? 'alert-danger' : 'alert-secondary'">
                    <div data-bind="text: createGroupMessage"></div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <td class="groupCell">
              <div class="row groupRowPadding">
                <div class="col-12">
                  <div class="groupTitle">Group Join Requests For Groups I Manage:</div>
                </div>
              </div>
              <div class="row groupRowPadding">
                <div class="col-12">
                  <table style="width: 100%" data-bind="visible: requests().length > 0">
                    <tbody data-bind="foreach: requests">
                      <tr>
                        <td class="groupMessage">
                          Player '<span data-bind="text: name"></span>' wants to join the '<span data-bind="text: description"></span>' group.
                        </td>
                        <td>
                          <button class="btn btn-outline-primary btn-sm" data-bind="click: $parent.serviceAccept, attr: { id: 'acc' + id }">Accept</button> &nbsp;
                          <button class="btn btn-outline-primary btn-sm" data-bind="click: $parent.serviceDeny, attr: { id: 'den' + id }">Deny</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <div style="display:none" data-bind="visible: requests().length == 0">
                    <span class="groupName">There are no outstanding group join requests.</span>
                  </div>
                </div>
              </div>
              <div class="row groupRowPadding">
                <div class="col-12">
                  <div class="alert groupMessage" style="display:none" data-bind="visible: joinRequestMessage().length > 0, class: isJoinRequestError() ? 'alert-danger' : 'alert-secondary'">
                    <div data-bind="text: joinRequestMessage"></div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <?php
  include('content/js/partials/app.php');
  include('content/js/models/manageGroups.php');
  include('content/js/manageGroups.php')
  ?>

</body>

</html>