
alter table `GameDeal` drop column `DateInserted`;

alter table `Game` 
  drop column `DateInserted`,
  drop column `Trump`,
  drop column `AJP`,
  drop column `AJR`,
  drop column `AJL`
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

drop table if exists `Play`;
create table `Play` (
  `ID` varchar(38) not null,
  `GameID` varchar(38) not null, 
  `Position` char(1) not null,
  `CardID1` char(3) not null, -- card id - third char ' ' means not been played.
  `CardID2` char(3) not null, -- card id - third char in ('1','2','3','4','5') indicates the order the player played his/her cards.
  `CardID3` char(3) not null, 
  `CardID4` char(3) not null, 
  `CardID5` char(3) not null, 
  `InsertDate` datetime not null,
  constraint `FK_Play_Game` foreign key (`GameID`) references `Game`(`ID`)
);


