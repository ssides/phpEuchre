
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

drop table if exists `UserProfile`;
create table `UserProfile`
(
  `ID` varchar(38) not null primary key, 
  `PlayerID` varchar(38) not null,
  `FileName` varchar(256) not null, 
  `OriginalImage` longtext not null,
  `ContentType` varchar(256) not null,
  `FileSize` int(10) not null,
  `InsertDate` datetime not null,
  `OriginalScale` decimal null,
  `Thumbnail` longtext null, 
  `HOffset` int null,
  `VOffset` int null,
  `DisplayScale` decimal null,
  constraint `FK_UserProfile_Player` foreign key (`PlayerID`) references `Player`(`ID`)
);

