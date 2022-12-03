
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

    self.pickItUp = function () { };
    self.selectCard = function () { };
    self.discard = function () { };
    self.play = function () { };
    self.pass = function () { };

}

