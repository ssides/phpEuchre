<script type="text/javascript">

  function isTouchDevice() {
    const hasMouse = window.matchMedia('(pointer: fine)').matches; // Fine pointer (mouse)
    return !hasMouse;
  }
  
  if (isTouchDevice()) {
    document.body.classList.add('touch-device');
    <?php echo "// this is a touch device\n"; ?>
  }

  function group(id, description) {
    this.id = id;
    this.description = description;
  }

  function loginViewModel() {
    var self = this;
   
    self.errorMessage = ko.observable('');
    self.name = ko.observable('');
    self.selectedGroup = ko.observable();
    self.groups = ko.observableArray();
    
    // New autocomplete properties
    self.suggestions = ko.observableArray([]);
    self.showSuggestions = ko.observable(false);
    self.highlightIndex = ko.observable(-1);

    self.getGroups = function() {
      $.ajax({
        method: 'POST',
        url: 'api/groups/getGroups.php',
        data: { },
        success: function (response) {
          let data = JSON.parse(response);
          if (data.ErrorMsg) {
            console.log(data.ErrorMsg);
          }
          let g = [];
          data.Groups.forEach(function (i) {
            g.push(new group(i[0], i[1]));
          });
          self.groups(g);
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          console.log(error);
        },
        complete: function(){
          self.invitationInProgress = false;
        }
      });
    };
    
    self.searchNames = function() {
        var term = self.name().trim();
        
        if (term.length < 2) {
            self.suggestions([]);
            self.showSuggestions(false);
            return;
        }

        $.ajax({
            method: 'GET',
            url: 'api/players/searchNames.php',
            data: { term: term },
            success: function (response) {
                let data = typeof response === 'string' ? JSON.parse(response) : response;
                self.suggestions(data.names || []);
                self.showSuggestions(data.names && data.names.length > 0);
                self.highlightIndex(-1);
            },
            error: function() {
                self.suggestions([]);
                self.showSuggestions(false);
            }
        });
    };

    // Select a suggestion
    self.selectSuggestion = function(name) {
        self.name(name);
        self.suggestions([]);
        self.showSuggestions(false);
        $('#name_signin').focus();
    };

    // Keyboard navigation
    self.handleKeyDown = function(data, event) {
        if (!self.showSuggestions()) return true;

        var current = self.highlightIndex();

        switch(event.key) {
            case 'ArrowDown':
                self.highlightIndex(Math.min(current + 1, self.suggestions().length - 1));
                return false;
            case 'ArrowUp':
                self.highlightIndex(Math.max(current - 1, -1));
                return false;
            case 'Enter':
                if (current >= 0) {
                    self.selectSuggestion(self.suggestions()[current]);
                    return false;
                }
                break;
            case 'Escape':
                self.showSuggestions(false);
                return false;
        }
        return true;
    };

    self.setErrorMessage = function(e){
      self.errorMessage(e);
    };
    
    self.validatePage = function(){
      var msg = '';
      if (self.name().length == 0) {
        msg = 'Please enter a name.';
      }
      return msg;
    };
    
    self.validateSubmit = function() {
      var v = self.validatePage();
      if (v.length == 0) {
        $('#loginForm').submit();
      } else {
        self.errorMessage(v);
      }
    };
    
    self.initialize = function() {
        self.getGroups();
    };
   
    self.initialize();
  }

  $(function () {
    var vm = new loginViewModel();
    ko.applyBindings(vm);
    
    setTimeout(function() {
        $('#name_signin').focus().select();
    }, 250);
  });
</script>
