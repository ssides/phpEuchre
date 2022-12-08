-- this was supposed to be the delta from what I have locally to what I have in google cloud,
-- but I wasn't careful enough.  To deply, drop and recreate: gamedeal, play, gameplay, game;

alter table `GameDeal` drop column `DateInserted`;

alter table `Game` 
  drop column `DateInserted`,
  drop column `Trump`,
  drop column `AJP`,
  drop column `AJR`,
  drop column `AJL`
;

alter table `Game`
  drop column `CardDiscardedByDealer`,
  drop column `CardFaceUp`
;

alter table `Game` 
  drop column `ACO`,
  drop column `ACP`,
  drop column `ACL`,
  drop column `ACR`
;

alter table `Game` add (
  `Turn` char(1) null,     -- positions: 'O'rganizer, 'P'artner, opponent 'L'eft, opponent 'R'ight
  `OrganizerTrump` varchar(2) null,  -- 'D'iamonds 'S'pades, 'H'earts, 'C'lubs  phpEuchre\content\images\cards\D.png, etc. and 'A'lone or 'N'ot.
  `OpponentTrump` varchar(2) null  -- 'D'iamonds 'S'pades, 'H'earts, 'C'lubs  phpEuchre\content\images\cards\D.png, etc. and 'A'lone or 'N'ot.
);



alter table `Game` add (
  `PlayTo` int not null default 10  -- Game score to play to.
);

alter table `Game` add (
`CardFaceUp` varchar (5) null  -- [0..1] CardID turned face up at the end of the deal. [2]: 'D'eclined or ordered 'U'p. [3]: who ordered it up or who declared trump ('O','P','L','R'). Stick the dealer is hard coded everywhere. [4]: Alone, the player who is skipped: ('O','P','L','R'). (CardFaceUp.length tells the js code what to display).
);

-- alter table `Game` add (
  -- `ACO` varchar(1) null, -- Organizer 'A'cknowledges a card played
  -- `ACP` varchar(1) null, -- Partner 'A'cknowledges first Jack or any card played
  -- `ACL` varchar(1) null, -- Left 'A'cknowledges first Jack or any card played
  -- `ACR` varchar(1) null  -- Right 'A'cknowledges first Jack or any card played
-- );

alter table `Game` add (
  `ACO` varchar(3) null, -- 'PLR' (any order) Partner acknowledges a card played by other players.
  `ACP` varchar(3) null, -- 'A' - partner acknowledges first Jack -- 'OLR' (any order) Partner acknowledges a card played by other players.
  `ACL` varchar(3) null, -- 'A' - left acknowledges first Jack -- 'OPR' (any order) Left acknowledges a card played by other players.
  `ACR` varchar(3) null, -- 'A' - right acknowledges first Jack -- 'OPL' (any order) Right acknowledges a card played by other players.
  `PO`  varchar(2) null, -- Card played by organizer.
  `PP`  varchar(2) null, -- Card played by partner.
  `PL`  varchar(2) null, -- Card played by left.
  `PR`  varchar(2) null  -- Card played by right.
);

alter table `Game` add (
  `Lead` varchar(2) null -- Card lead.
);

alter table `GamePlay` add (`Alone` char(1) not null);

alter table `Game` add (
  `FinishDate` datetime null
);

update `Game` set finishdate=datefinished;

alter table `Game` 
  drop column `DateFinished`
;

alter table `Game` add (
  `GameFinishDate` datetime null
);

update `Game` set GameFinishDate=finishdate;

alter table `Game` 
  drop column `finishdate`
;

alter table `Game` add (
  `ScoringInProgress` enum('0','1') NOT NULL default '0'  -- '1' if scoring is in progress, '0' otherwise.
);

