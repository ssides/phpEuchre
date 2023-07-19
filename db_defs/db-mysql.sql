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
  `OrganizerTricks` int null,
  `OpponentTricks` int null,
  `GameStartDate` datetime null, 
  `GameFinishDate` datetime null,
  `PartnerInviteDate` datetime null, 
  `PartnerJoinDate` datetime null, 
  `LeftInviteDate` datetime null, 
  `LeftJoinDate` datetime null, 
  `RightInviteDate` datetime null, 
  `RightJoinDate` datetime null, 
  `Dealer` varchar(1) null,  --  'O'rganizer 'P'artner, 'L'eft, 'R'ight
  `Turn` char(1) null,     -- positions: 'O'rganizer, 'P'artner, opponent 'L'eft, opponent 'R'ight
  `Lead` varchar(2) null, -- Card lead.
  `OrganizerTrump` varchar(2) null,  -- 'D'iamonds 'S'pades, 'H'earts, 'C'lubs  phpEuchre\content\images\cards\D.png, etc. and 'A'lone or 'N'ot.
  `OpponentTrump` varchar(2) null,  -- 'D'iamonds 'S'pades, 'H'earts, 'C'lubs  phpEuchre\content\images\cards\D.png, etc. and 'A'lone or 'N'ot.
  `FirstDealPosition` char(1) null,
  `FirstJackIndex` int null, 
  `FirstJackPosition` varchar(1) null, --  'O'rganizer 'P'artner, opponent 'L'eft, opponent 'R'ight
  `ACO` varchar(3) null, -- Organizer acknowledges a card played by 'P'artner, 'L'eft, and 'R'ight.
  `ACP` varchar(3) null, -- Partner 'A'cknowledges first Jack or scoring in progress or card played by 'O'rganizer, 'L'eft, and 'R'ight.
  `ACL` varchar(3) null, -- Left 'A'cknowledges first Jack or scoring in progress or card played by 'O'rganizer, 'P'artner, and 'R'ight.
  `ACR` varchar(3) null, -- Right 'A'cknowledges first Jack or scoring in progress or card played by 'O'rganizer, 'P'artner, and 'L'eft.
  `PO`  varchar(2) null, -- Card played by organizer.
  `PP`  varchar(2) null, -- Card played by partner.
  `PL`  varchar(2) null, -- Card played by left.
  `PR`  varchar(2) null, -- Card played by right.
  `InsertDate` datetime not null,
  `PlayTo` int not null default 10, -- Game score to play to.
  `CardFaceUp` char(5) null,  -- [0..1] CardID turned face up at the end of the deal. [2]: 'D'eclined, ordered 'U'p, u'S'ed by dealer or dealer s'K'ipped. [3]: who ordered it up or who declared trump ('O','P','L','R').  [4]: The partner of the player who called it alone ('O','P','L','R'). (Stick the dealer is hard coded everywhere. CardFaceUp.length tells the js code what to display).
  `ScoringInProgress` enum('0','1') not null default '0',  -- '1' if scoring is in progress, '0' otherwise.
  `AcknowledgeScoring` varchar(3) null, -- P,L,R need to acknowledge scoring in progress.
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
  `IsActive` enum('0','1') not null default '0',
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

-- could let the organizer replay the hand or game someday. also helps with verifying that scoring is correct.
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
  `OrganizerTrump` char(1) not null, -- Suit the Organizer or Partner named or '-'
  `OpponentTrump` char(1) not null,  -- Suit the Right or Left player named or '-'
  `OrganizerScore` int not null,
  `OpponentScore` int not null,
  `OrganizerTricks` int not null,
  `OpponentTricks` int not null,
  `Alone` char(1) not null, -- 'A'lone or '-' not alone.
  `InsertDate` datetime not null,
  constraint `FK_GamePlay_Game` foreign key (`GameID`) references `Game`(`ID`),
  constraint `FK_GamePlay_Deal` foreign key (`DealID`) references `Deal`(`ID`)
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
  `Dealer` varchar(1) null,  --  'O'rganizer 'P'artner, 'L'eft, 'R'ight
  `Turn` char(1) null,     -- positions: 'O'rganizer, 'P'artner, opponent 'L'eft, opponent 'R'ight
  `CardFaceUp` char(5) null,  -- [0..1] CardID turned face up at the end of the deal. [2]: 'D'eclined, ordered 'U'p, u'S'ed by dealer or dealer s'K'ipped. [3]: who ordered it up or who declared trump ('O','P','L','R').  [4]: The partner of the player who called it alone ('O','P','L','R'). (Stick the dealer is hard coded everywhere. CardFaceUp.length tells the js code what to display).
  `ACO` varchar(3) null, -- Organizer acknowledges a card played by 'P'artner, 'L'eft, and 'R'ight.
  `ACP` varchar(3) null, -- Partner 'A'cknowledges first Jack or scoring in progress or card played by 'O'rganizer, , 'L'eft, and 'R'ight.
  `ACL` varchar(3) null, -- Left 'A'cknowledges first Jack or scoring in progress or card played by 'O'rganizer, , 'P'artner, and 'R'ight.
  `ACR` varchar(3) null, -- Right 'A'cknowledges first Jack or scoring in progress or card played by 'O'rganizer, , 'P'artner, and 'L'eft.
  `PO`  varchar(2) null, -- Card played by organizer.
  `PP`  varchar(2) null, -- Card played by partner.
  `PL`  varchar(2) null, -- Card played by left.
  `PR`  varchar(2) null,  -- Card played by right.
  constraint `FK_GameControllerLog_Game` foreign key (`GameID`) references `Game`(`ID`),
  constraint `FK_GameControllerLog_Deal` foreign key (`DealID`) references `Deal`(`ID`)
);
