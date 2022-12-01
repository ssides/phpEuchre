use Euchre;

drop table if exists `Player`;
create table `Player` (
  `ID` varchar(38) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Token` varchar(255) NOT NULL,
  `IsActive` enum('0','1') NOT NULL,
  `InsertDate` datetime NOT NULL
);

create unique index ix_PlayerID on `Player`(`ID`);
create unique index ix_PlayerName on `Player`(`Name`);

drop table if exists `UserProfile`;
create table `UserProfile`
(
  `ID` varchar(38) not null primary key, 
  `PlayerID` varchar(38) not null,
  `OriginalName` varchar(256) not null,
  `OriginalSavedPath` varchar(256) not null,
  `OriginalContentType` varchar(256) not null,
  `OriginalFileSize` int(10) not null,
  `InsertDate` datetime not null,
  `OriginalScale` decimal(15,13) null,
  `DisplayScale` decimal(15,13) null,
  `ThumbnailPath` varchar(256) null, 
  `HOffset` int null,
  `VOffset` int null,
  constraint `FK_UserProfile_Player` foreign key (`PlayerID`) references `Player`(`ID`)
);

drop table if exists `Deal`;
create table `Deal`
(
  `ID` varchar(38) not null primary key,
  `Cards` char(72) not null, 
  `PurposeCode` char not null default 'D'
);

create unique index ix_Deal_Cards on `Deal`(`Cards`);

drop table if exists `Game`;
create table `Game`
(
  `ID` varchar(38) not null primary key, 
  `Organizer` varchar(450) not null, --  Organizer, Partner, Left, Right should all be varchar(38) to match PlayerID but we can't change it now.
  `Partner` varchar(450) null, 
  `Left` varchar(450) null, 
  `Right` varchar(450) null,
  `OrganizerScore` int not null, 
  `OpponentScore` int not null, 
  `DateFinished` datetime null,
  `GameStartDate` datetime null, 
  `PartnerInviteDate` datetime null, 
  `PartnerJoinDate` datetime null, 
  `LeftInviteDate` datetime null, 
  `LeftJoinDate` datetime null, 
  `RightInviteDate` datetime null, 
  `RightJoinDate` datetime null, 
  `Dealer` varchar(1) null,  --  'O'rganizer 'P'artner, 'L'eft, 'R'ight
  `OrganizerTrump` varchar(2) null,  -- 'D'iamonds 'S'pades, 'H'earts, 'C'lubs  phpEuchre\content\images\cards\D.png, etc. and 'A'lone or 'N'ot.
  `OpponentTrump` varchar(2) null,  -- 'D'iamonds 'S'pades, 'H'earts, 'C'lubs  phpEuchre\content\images\cards\D.png, etc. and 'A'lone or 'N'ot.
  `FirstJackIndex` int null, 
  `FirstJackPosition` varchar(1) null, --  'O'rganizer 'P'artner, opponent 'L'eft, opponent 'R'ight
  `ACO` varchar(1) null, -- Organizer 'A'cknowledges a card played
  `ACP` varchar(1) null, -- Partner 'A'cknowledges first Jack or any card played
  `ACL` varchar(1) null, -- Left 'A'cknowledges first Jack or any card played
  `ACR` varchar(1) null, -- Right 'A'cknowledges first Jack or any card played
  `OrganizerTricks` int null, -- Score
  `OpponentTricks` int null,
  `InsertDate` datetime not null,
  `FirstDealPosition` char(1) null,
  `CardFaceUp` char(5) null,  -- [0..1] CardID turned face up at the end of the deal. [2]: 'D'eclined, ordered 'U'p, or u'S'ed by dealer. [3]: who ordered it up or who declared trump ('O','P','L','R').  [4]: 'A'lone. (Stick the dealer is hard coded everywhere. CardFaceUp.length tells the js code what to display).
  constraint `FK_GameOrg_Player` foreign key (`Organizer`) references `Player`(`ID`),
  constraint `FK_GameParter_Player` foreign key (`Partner`) references `Player`(`ID`),
  constraint `FK_GameLeft_Player` foreign key (`Left`) references `Player`(`ID`),
  constraint `FK_GameRight_Player` foreign key (`Right`) references `Player`(`ID`)
  constraint `FK_GameDealer_Player` foreign key (`Dealer`) references `Player`(`ID`)
 );

drop table if exists `GameDeal`;
create table `GameDeal`
(
  `ID` varchar(38) not null primary key, 
  `GameID` varchar(38) not null, 
  `DealID` varchar(38) not null, 
  `InsertDate` datetime not null,
  constraint `FK_GameDeal_Game` foreign key (`GameID`) references `Game`(`ID`), 
  constraint `FK_GameDeal_Deal` foreign key (`DealID`) references `Deal`(`ID`)
);

drop table if exists `Play`;
create table `Play` (
  `ID` varchar(38) not null,
  `GameID` varchar(38) not null, 
  `Position` char(1) not null,
  `CardID1` char(3) not null, -- card id - length == 2 means not been played.
  `CardID2` char(3) not null, -- card id - third char == 'P' indicates the card has been played.
  `CardID3` char(3) not null, 
  `CardID4` char(3) not null, 
  `CardID5` char(3) not null, 
  `InsertDate` datetime not null,
  constraint `FK_Play_Game` foreign key (`GameID`) references `Game`(`ID`)
);

-- could let the organizer replay the hand or game someday.
drop table if exists `GamePlay`;
create table `GamePlay`
(
  `ID` varchar(38) not null primary key, 
  `GameID` varchar(38) not null, 
  `DealID` varchar(38) not null, 
  `Lead` char(1) not null, -- 'OPLR'
  `CardO` char(2) not null, -- card id played by organizer.
  `CardP` char(2) not null, -- card id played by partner.
  `CardL` char(2) not null, -- card id played by left.
  `CardR` char(2) not null, -- card id played by right.
  `InsertDate` datetime not null,
  constraint `FK_GamePlay_Game` foreign key (`GameID`) references `Game`(`ID`),
  constraint `FK_GamePlay_Deal` foreign key (`DealID`) references `Deal`(`ID`)
);
