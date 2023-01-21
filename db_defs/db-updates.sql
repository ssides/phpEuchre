

alter table `GameControllerLog` add (
  `Dealer` varchar(1) null,  --  'O'rganizer 'P'artner, 'L'eft, 'R'ight
  `Turn` char(1) null,     -- positions: 'O'rganizer, 'P'artner, opponent 'L'eft, opponent 'R'ight
  `CardFaceUp` char(5) null,  -- [0..1] CardID turned face up at the end of the deal. [2]: 'D'eclined, ordered 'U'p, u'S'ed by dealer or dealer s'K'ipped. [3]: who ordered it up or who declared trump ('O','P','L','R').  [4]: The partner of the player who called it alone ('O','P','L','R'). (Stick the dealer is hard coded everywhere. CardFaceUp.length tells the js code what to display).
  `ACO` varchar(3) null, -- Organizer acknowledges a card played by 'P'artner, 'L'eft, and 'R'ight.
  `ACP` varchar(3) null, -- Partner 'A'cknowledges first Jack or scoring in progress or card played by 'O'rganizer, , 'L'eft, and 'R'ight.
  `ACL` varchar(3) null, -- Left 'A'cknowledges first Jack or scoring in progress or card played by 'O'rganizer, , 'P'artner, and 'R'ight.
  `ACR` varchar(3) null, -- Right 'A'cknowledges first Jack or scoring in progress or card played by 'O'rganizer, , 'P'artner, and 'L'eft.
  `PO`  varchar(2) null, -- Card played by organizer.
  `PP`  varchar(2) null, -- Card played by partner.
  `PL`  varchar(2) null, -- Card played by left.
  `PR`  varchar(2) null  -- Card played by right.
);
