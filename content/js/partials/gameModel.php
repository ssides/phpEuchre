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
    this.ScoringInProgress = (data.ScoringInProgress || '') === '1';
    this.DealID = data.DealID || '';
    
    this.getAllCards = function(){ return this.PO + this.PP + this.PL + this.PR;  };
    
    // Acknowledgements of played cards.
    this.getAllAcknowledgments = function(){ return this.ACO + this.ACP + this.ACL + this.ACR; };
    
    // Acknowledgements of first Jack or scoring in progress.
    this.allPlayersHaveAcknowledged = function() {
      return this.ACP == 'A' && this.ACR == 'A' && this.ACL == 'A';
    };

    // todo: come up with a short name for allCardsHaveBeenPlayedAndAcknowledged()
    // maybe allCardsPlayedAndSeen
    this.allCardsHaveBeenPlayed = function() {
      if (this.CardFaceUp.length == 5) {
        // If there are only three players, there are only two acknowledgments per player.
        // The skipped player will probably see all the cards played, but there is no mechanism 
        // to guarantee that.
        return this.getAllCards().length == 6 && this.getAllAcknowledgments().length == 6;
      } else {
        return this.getAllCards().length == 8 && this.getAllAcknowledgments().length == 12;
      }
    };
    
    this.preScoring = function() {
      if (this.CardFaceUp.length == 5) {
        return this.getAllCards().length == 6;
      } else {
        return this.getAllCards().length == 8;
      }
    };
    
  }

</script>
