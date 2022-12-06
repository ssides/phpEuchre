
// Most of the Euchre rules are coded here. The update() method is called on every 
// heartbeat, and determines the game state using several fields in the gameModel.
// This viewmodel controls what happens inside the SouthInfo div block in play.php.
// Popping up the dialog that lets the user select trump, whenever that is necessary
// is handled by gameController, not currentPlayerInfoViewModel.
function currentPlayerInfoViewModel() {
    var self = this;

    self.gameData = new gameModel({});
    self.showPassBtn = ko.observable(true);
    self.enablePassBtn = ko.observable(true);
    self.showPlayBtn = ko.observable(true);
    self.enablePlayBtn = ko.observable(true);
    self.showPickItUpGroup = ko.observable(true);
    self.enablePickItUpGroup = ko.observable(true);
    self.showDiscardBtn = ko.observable(true);
    self.enableDiscardBtn = ko.observable(true);
    self.dealer = ko.observable('D');
    self.isMyTurn = ko.observable(false);
    self.obsAlone = ko.observable();
    self.cards = ko.observableArray();
    self.sortCardsCompareFn = function (a, b) { return a.suit === b.suit ? (a.rank == b.rank ? 0 : a.rank < b.rank ? -1 : 1) : a.suit < b.suit ? -1 : 1; };
    self.sortedCards = ko.pureComputed(function () {
        return self.cards.slice().sort(self.sortCardsCompareFn);
    });

    self.selectCard = function (card) {
        self.cards().forEach(function (c) {
            if (c.id == card.id && c.isPlayable()) {
                c.isSelected(!c.isSelected());
            } else {
                c.isSelected(false);
            }
        });
    };
    self.pickItUp = function () { self.enablePickItUpGroup(false); };
    self.discard = function () { self.enableDiscardBtn(false); };
    self.play = function () { self.enablePlayBtn(false); };
    self.pass = function () { self.enablePassBtn(false); };

    self.update = function (gameData, dealID) {

        self.gameData = new gameModel(gameData);
        self.iamDealer = self.myPosition == gameData.Dealer;
        self.trump = self.gameData.OpponentTrump || self.gameData.OrganizerTrump;
        self.dealID = dealID;
        self.pickingItUp = self.gameData.CardFaceUp.length > 2 && self.gameData.CardFaceUp[2] == 'U' && self.iamDealer;
        self.discarded = self.gameData.CardFaceUp.length > 2 && self.gameData.CardFaceUp[2] == 'S' && self.iamDealer;

        if (self.myPosition == gameData.Dealer) {
            self.dealer('D');
        } else {
            self.dealer(' ');
        }

        self.isMyTurn(self.myPosition == gameData.Turn);

        var updateReason = '';

        if (self.dealID != self.previousDealID) {
            self.previousDealID = self.dealID;
            updateReason += 'D';  // new 'D'eal
        }

        if (self.trump != self.previousTrump) {
            self.previousTrump = self.trump;
            updateReason += 'T';  // new 'T'rump
        }

        if (gameData.CardFaceUp != self.previousCardFaceUp) {
            self.previousCardFaceUp = gameData.CardFaceUp;
            updateReason += 'U'; // new CardFace'U'p
        }

        if (self.gameData.getAllCards() != self.previousCards) {
            self.previousCards = self.gameData.getAllCards();
            updateReason += 'C'; // 'C'ards played.
        }

        if (self.previousTurn != gameData.Turn) {
            self.previousTurn = gameData.Turn;
            updateReason += 'R'; // change in tu'R'n
        }
    };

    self.getSuitOrder = function (c) {
        if (self.trump) {
            var order = 5;
            if (c[0] == 'J') {
                switch (self.trump) {
                    case 'D':
                    case 'H':
                        if (c[1] == 'D' || c[1] == 'H') {
                            order = 1;
                        } else {
                            switch (self.trump) {
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
                            switch (self.trump) {
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
                switch (self.trump) {
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

    self.getRank = function (c) {
        if (self.trump && c[0] == 'J') {
            var order = 15;
            switch (self.trump) {
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
            return r == '9' ? 14 : r == '1' ? 13 : r == 'J' ? 12 : r == 'Q' ? 11 : r == 'K' ? 10 : 9;
        }
    };


    self.getCardObject = function (c) {
        var o = {
            id: c,
            url: app.getCardURL(c),
            suit: self.getSuitOrder(c),
            rank: self.getRank(c),
            isPlayable: ko.observable(true),
            isSelected: ko.observable(false)
        };

        return o;
    };



    self.initialize = function (selfPosition, gameData) {
        self.myPosition = selfPosition;

        var cards = [];

        self.trump = 'D';
        var c = self.getCardObject('JD');
        cards.push(c);
        var c = self.getCardObject('JH');
        cards.push(c);
        var c = self.getCardObject('AD');
        cards.push(c);
        var c = self.getCardObject('QH');
        c.isPlayable(false);
        cards.push(c);
        var c = self.getCardObject('KC');
        c.isPlayable(false);
        cards.push(c);

        self.cards(cards);
    };

    self.initialize();

}

