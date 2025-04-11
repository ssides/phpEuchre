<script type="text/javascript">

  function soundViewModel() {
    var self = this;

    self.soundTimer = null;
    self.showSoundIcon = ko.observable(false);
    self.soundMute = ko.observable(true);

    self.toggleSound = function(){
      if (self.soundMute()) {
        clearInterval(self.soundTimer);
        self.soundTimer = setInterval(app.soundPop, 100);
        self.soundMute(false);
      } else {
        clearInterval(self.soundTimer);
        self.soundTimer = setInterval(app.soundMute, 1000);
        self.soundMute(true);
      }
    };
    
    self.isSafari = function() {
      const ua = navigator.userAgent.toLowerCase();
      return ua.includes("safari") && !ua.includes("chrome") && !ua.includes("edg");
    };

    self.initialize = function(){
      self.soundTimer = setInterval(app.soundMute, 1000);
      self.showSoundIcon(!self.isSafari());
    }
    
    self.initialize();
  }
  
</script>
