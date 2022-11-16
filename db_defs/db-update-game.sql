
alter table `GameDeal` drop column `DateInserted`;

alter table `Game` 
  drop column `DateInserted`,
  drop column `Trump`;

alter table `Game` add (
  `Turn` char(1) null,     -- positions: 'O'rganizer, 'P'artner, opponent 'L'eft, opponent 'R'ight
  `OrganizerTrump` varchar(2) null,  -- 'D'iamonds 'S'pades, 'H'earts, 'C'lubs  phpEuchre\content\images\cards\D.png, etc. and 'A'lone or 'N'ot.
  `OpponentTrump` varchar(2) null  -- 'D'iamonds 'S'pades, 'H'earts, 'C'lubs  phpEuchre\content\images\cards\D.png, etc. and 'A'lone or 'N'ot.
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


