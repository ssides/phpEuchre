<script type="text/javascript">

  function request(id, groupID, description, playerID, name) {
    this.id = id;
    this.groupID = groupID;
    this.description = description;
    this.playerID = playerID;
    this.name = name;
  }
  
  function groupMemberOf(description, isManager) {
    this.description = description;
    this.isManager = isManager;
  }
  
  function groupNotAMemberOf(id, description) {
    this.groupID = id;
    this.description = description;
  }
  
</script>