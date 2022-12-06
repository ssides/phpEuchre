
function bidDialogViewModel() {
    var self = this;

    self.submitted = false;
    self.myPosition = '';
    self.showPassBtn = ko.observable(true);
    self.enablePassBtn = ko.observable(true);
    self.showSubmitBtn = ko.observable(true);
    self.enableSubmitBtn = ko.observable(true);
    self.suits = ko.observableArray();
    self.alone = ko.observable();

    self.selectSuit = function (suit) {
        self.suits().forEach(function (s) {
            if (s.id == suit.id && s.isPlayable()) {
                s.isSelected(!s.isSelected());
            } else {
                s.isSelected(false);
            }
        });
    };
    self.pass = function () { };
    self.submit = function () { };

    self.getSuitObject = function (s) {
        var o = {
            id: s,
            url: app.getCardURL(s),
            isPlayable: ko.observable(true),
            isSelected: ko.observable(false)
        };
        return o;
    };

    self.initialize = function () {
        var s = [];
        s.push(self.getSuitObject('D'));
        s.push(self.getSuitObject('C'));
        s.push(self.getSuitObject('H'));

        var suit = self.getSuitObject('S');
        suit.isPlayable(false);
        s.push(suit);

        self.suits(s);
    };

    self.initialize();
}

