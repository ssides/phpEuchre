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
  `Organizer` varchar(450) not null, 
  `Partner` varchar(450) null, 
  `Left` varchar(450) null, 
  `Right` varchar(450) null,
  `OrganizerScore` int not null, 
  `OpponentScore` int not null, 
  `DateInserted` datetime not null, 
  `DateFinished` datetime null,
  `GameStartDate` datetime null, 
  `PartnerInviteDate` datetime null, 
  `PartnerJoinDate` datetime null, 
  `LeftInviteDate` datetime null, 
  `LeftJoinDate` datetime null, 
  `RightInviteDate` datetime null, 
  `RightJoinDate` datetime null, 
  constraint `FK_GameOrg_Player` foreign key (`Organizer`) references `Player`(`ID`), 
  constraint `FK_GameParter_Player` foreign key (`Partner`) references `Player`(`ID`), 
  constraint `FK_GameLeft_Player` foreign key (`Left`) references `Player`(`ID`), 
  constraint `FK_GameRight_Player` foreign key (`Right`) references `Player`(`ID`)
 );
 
drop table if exists `GameDeal`;
create table `GameDeal`
(
  `ID` varchar(38) not null primary key, 
  `GameID` varchar(38) not null, 
  `DealID` varchar(38) not null, 
  `DateInserted` datetime not null,
  constraint `FK_GameDeal_Game` foreign key (`GameID`) references `Game`(`ID`), 
  constraint `FK_GameDeal_Deal` foreign key (`DealID`) references `Deal`(`ID`)
);
