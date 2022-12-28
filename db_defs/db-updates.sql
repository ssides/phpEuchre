
alter table `GameDeal` add (
  `IsActive` enum('0','1') not null default '0'
);


drop table if exists `GameControllerLog`;
create table `GameControllerLog`
(
  `ID` varchar(38) not null primary key, 
  `GameID` varchar(38) not null,
  `DealID` varchar(38) null,
  `PositionID` varchar(1) not null,
  `GameControllerState` varchar(100) not null,  -- todo: make this a reference to a GameControllerState table.
  `Message` varchar(4096) null,  
  `OrganizerScore` int not null, 
  `OpponentScore` int not null, 
  `OrganizerTricks` int not null,
  `OpponentTricks` int not null,
  `InsertDate` datetime not null,
  constraint `FK_GameControllerLog_Game` foreign key (`GameID`) references `Game`(`ID`),
  constraint `FK_GameControllerLog_Deal` foreign key (`DealID`) references `Deal`(`ID`)
);

