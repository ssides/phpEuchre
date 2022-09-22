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
  `ID` int not null primary key,
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
  `Dealer` varchar(1) null,  --  'N' not set.  'O'rganizer 'P'artner, 'L'eft, 'R'ight
  `Trump` varchar(2) null,  -- 'D'iamonds 'S'pades, 'H'earts, 'C'lubs  phpEuchre\content\images\cards\D.jpg, etc. and position who named trump.
  `FirstJackIndex` int null, 
  `FirstJackPosition` varchar(1) null, --  'O'rganizer 'P'artner, opponent 'L'eft, opponent 'R'ight
  `AJP` varchar(1) null, -- Partner 'A'cknowledges first Jack
  `AJR` varchar(1) null, -- opponent Right 'A'cknowledges first Jack
  `AJL` varchar(1) null, -- opponent Left 'A'cknowledges first Jack
  `OrganizerTricks` int null,
  `OpponentTricks` int null,
  `InsertDate` datetime not null,
  `FirstDealPosition` char(1) null,
  `CardDiscardedByDealer` char(2) null,
  `CardFaceUp` char(4) null, -- CardID turned face up at the end of the deal. third char: 'D'eclined or ordered 'U'p. fourth char position who ordered it up or who named trump.
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
  `DealID` int not null, 
  `InsertDate` datetime not null,
  constraint `FK_GameDeal_Game` foreign key (`GameID`) references `Game`(`ID`), 
  constraint `FK_GameDeal_Deal` foreign key (`DealID`) references `Deal`(`ID`)
);

