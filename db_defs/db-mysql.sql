DROP TABLE IF EXISTS `users`;


drop table if exists `Players`;
create table `Players` (
  `PlayerID` char(38) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Token` varchar(255) NOT NULL,
  `IsActive` enum('0','1') NOT NULL,
  `InsertDate` datetime NOT NULL
);

create unique index ix_PlayerID on `Players`(`PlayerID`)


