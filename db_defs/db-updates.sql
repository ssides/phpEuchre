
drop table if exists `GroupRequest`;
drop table if exists `PlayerGroup`;
drop table if exists `Group`;
create table `Group`
(
  `ID` varchar(38) not null primary key,
  `Description` varchar(150) not null,
  `ManagerID` varchar(38) not null,
  `IsActive` enum('0','1') not null default '0',  -- if a group is marked inactive, no one can request to join it.
  `InsertDate` datetime not null,
  constraint `FK_Group_Player` foreign key (`ManagerID`) references `Player`(`ID`)
);

create table `GroupRequest`
(
  `ID` varchar(38) not null primary key,
  `PlayerID` varchar(38) not null,
  `GroupID` varchar(38) not null,
  `IsActive` enum('R','A','D') not null default 'R',  -- R)equested, A)ctive, D)eclined
  `InsertDate` datetime not null,
  constraint `FK_GroupRequest_Player` foreign key (`PlayerID`) references `Player`(`ID`),
  constraint `FK_GroupRequest_Group` foreign key (`GroupID`) references `Group`(`ID`)
);

-- IsActive is inconsistently implemented throughout the app. Todo: fix it.
-- `PlayerGroup`.`IsActive` can only be updated by running a sql statement.
-- Setting it to '0' will prevent the player from being invited to a game
-- of that group. But everything else will still work.  The player can log 
-- in, request to join another group, and be added to other groups, and be 
-- invited to games in other groups, etc.  This feature may never be needed.
create table `PlayerGroup`
(
  `ID` varchar(38) not null primary key,
  `PlayerID` varchar(38) not null,
  `GroupID` varchar(38) not null,
  `IsActive` enum('0','1') not null default '0', -- Player cannot be invited to a game if '0'.
  `InsertDate` datetime not null,
  constraint `FK_PlayerGroup_Player` foreign key (`PlayerID`) references `Player`(`ID`),
  constraint `FK_PlayerGroup_Group` foreign key (`GroupID`) references `Group`(`ID`)
);

create unique index ix_PlayerGroup on `PlayerGroup`(`PlayerID`,`GroupID`);

-- this will be different during deployment.

insert into `Group` (ID,Description,ManagerID,IsActive,InsertDate) values ('D7F84F8D-E653-41AD-8D7E-707F748EF2A6','Sides Family','38D91D69-2F1E-4D84-A16B-7918D8DEC187','1',now());

insert into `PlayerGroup` (ID,PlayerID,GroupID,IsActive,InsertDate) values ('06AF98E5-2E4A-498A-95DC-C5F9A720D2C4','38D91D69-2F1E-4D84-A16B-7918D8DEC187','D7F84F8D-E653-41AD-8D7E-707F748EF2A6','1',now());

-- write inserts to `PlayerGroup` for everyone so the manager doesn't need to do it.