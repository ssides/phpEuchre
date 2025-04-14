<script type="text/javascript">

  cardSort = {};

  cardSort.sortCardsCompareFn = function(a,b){ return a.suit === b.suit ? (a.rank == b.rank ? 0 : a.rank < b.rank ? -1 : 1) : a.suit < b.suit ? -1 : 1; };

  cardSort.getSuitOrder = function(trump, c) {
    if (trump) {
      var order = 5;
      if (c[0] == 'J') {
        switch(trump) {
          case 'D':
          case 'H':
            if (c[1] == 'D' || c[1] == 'H') {
              order = 1;
            } else {
              switch(trump) {
                case 'H':
                  order = c[1] == 'C' ? 4 : 2;
                  break;
                case 'D':
                  order = c[1] == 'C' ? 4 : 3;
                  break;
              }
            }
            break;
          case 'S':
          case 'C':
            if (c[1] == 'S' || c[1] == 'C') {
              order = 1;
            } else {
              switch(trump) {
                case 'S':
                  order = c[1] == 'D' ? 3 : 2;
                  break;
                case 'C':
                  order = c[1] == 'D' ? 4 : 2;
                  break;
              }
            }
            break;
        }
      } else {
        switch(trump) {
          case 'D':
            order = c[1] == 'D' ? 1 : c[1] == 'C' ? 4 : c[1] == 'H' ? 2 : 3;
            break;
          case 'H':
            order = c[1] == 'D' ? 3 : c[1] == 'C' ? 4 : c[1] == 'H' ? 1 : 2;
            break;
          case 'S':
            order = c[1] == 'D' ? 3 : c[1] == 'C' ? 4 : c[1] == 'H' ? 2 : 1;
            break;
          case 'C':
            order = c[1] == 'D' ? 4 : c[1] == 'C' ? 1 : c[1] == 'H' ? 2 : 3;
            break;
        }
      }
      return order;
    } else {
      var s = c[1];
      return s == 'D' ? 1 : s == 'C' ? 2 : s == 'H' ? 3 : 4;
    }
  };
  
  cardSort.getRank = function(trump, c) {
    if (trump && c[0] == 'J') {
      var order = 15;
      switch(trump) {
        case 'D':
          order = c[1] == 'D' ? 1 : c[1] == 'H' ? 2 : 12;
          break;
        case 'H':
          order = c[1] == 'D' ? 2 : c[1] == 'H' ? 1 : 12;
          break;
        case 'S':
          order = c[1] == 'S' ? 1 : c[1] == 'C' ? 2 : 12;
          break;
        case 'C':
          order = c[1] == 'S' ? 2 : c[1] == 'C' ? 1 : 12;
          break;
      }
      return order;
    } else {
      var r = c[0];
      return r == '9' ? 14 : r == '1' ? 13 : r == 'J' ? 12 : r == 'Q' ? 11 :r == 'K' ? 10 : 9;
    }
  };

  cardSort.getCardObject = function(trump, c) {
    var o = {
      id: c, 
      url: app.getCardURL(c),
      suit: cardSort.getSuitOrder(trump, c),
      rank: cardSort.getRank(trump, c),
      isPlayable: ko.observable(true),
      isSelected: ko.observable(false)
    };
    
    return o;
  };

</script>