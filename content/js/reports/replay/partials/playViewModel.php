<script type="text/javascript">
  
  function playViewModel() {
    var self = this;
    
    self.organizerCardURL = ko.observable('');
    self.partnerCardURL = ko.observable('');
    self.leftCardURL = ko.observable('');
    self.rightCardURL = ko.observable('');
    
    self.playCard = function(position, cardID) {
      switch (position) {
        case 'O': self.organizerCardURL(app.getCardURL(cardID)); break;
        case 'P': self.partnerCardURL(app.getCardURL(cardID)); break;
        case 'L': self.leftCardURL(app.getCardURL(cardID)); break;
        case 'R': self.rightCardURL(app.getCardURL(cardID)); break;
      }
    };
    
    self.clearTable = function() {
      self.organizerCardURL('');
      self.partnerCardURL('');
      self.leftCardURL('');
      self.rightCardURL('');
    };
  }

</script>