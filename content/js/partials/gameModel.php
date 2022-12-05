<script type="text/javascript">

  function gameModel(data) {
    
    this.Organizer = data.Organizer || '';
    this.Partner = data.Partner || '';
    this.Left = data.Left || '';
    this.Right = data.Right || '';
    this.Dealer = data.Dealer || '';
    this.Turn = data.Turn || '';
    this.Lead = data.Lead || '';
    this.CardFaceUp = data.CardFaceUp || '';
    this.OrganizerTrump = data.OrganizerTrump || '';
    this.OrganizerTricks = data.OrganizerTricks || '';
    this.OrganizerScore = data.OrganizerScore || '';
    this.OpponentTrump = data.OpponentTrump || '';
    this.OpponentTricks = data.OpponentTricks || '';
    this.OpponentScore = data.OpponentScore || '';
    this.PlayTo = data.PlayTo | '';
    this.ACO = data.ACO || '';
    this.ACP = data.ACP || '';
    this.ACR = data.ACR || '';
    this.ACL = data.ACL || '';
    this.PO = data.PO || '';
    this.PP = data.PP || '';
    this.PL = data.PL || '';
    this.PR = data.PR || '';
    this.OThumbnailURL = data.OThumbnailURL || '';
    this.OName = data.OName || '';
    this.PThumbnailURL = data.PThumbnailURL || '';
    this.PName = data.PName || '';
    this.LThumbnailURL = data.LThumbnailURL || '';
    this.LName = data.LName || '';
    this.RThumbnailURL = data.RThumbnailURL || '';
    this.RName = data.RName || '';
    this.GameStartDate = data.GameStartDate || '';
    this.GameFinishDate = data.GameFinishDate || '';
    
    this.getAllCards = function(){ return this.PO + this.PP + this.PL + this.PR;  };
    
    this.getAllAcknowledgments = function(){
      return this.ACO + this.ACP + this.ACL + this.ACR;
    };
    
    this.allCardsHaveBeenPlayed = function() {
      if (this.CardFaceUp.length == 5) {
        // If there are only three players, there are only two acknowledgments per player.
        // The skipped player will probably see all the cards, but there is no mechanism 
        // to guarantee that.
        return this.getAllCards().length == 6 && this.getAllAcknowledgments().length == 6;
      } else {
        return this.getAllCards().length == 8 && this.getAllAcknowledgments().length == 12;
      }
    };

  }
  
</script>
