alter table `Game` add (
  `Dealer` varchar(1) null,  --  O)rganizer P)artner, L)eft, R)ight
  `Trump` varchar(1) null,  --  D)iamonds S)pades, H)earts, C)lubs
  `FirstJackIndex` int null, 
  `FirstJackPosition` varchar(1) null, --  O)rganizer P)artner, L)eft, R)ight
  `AJP` varchar(1) null, --  Partner acknowledges first Jack
  `AJR` varchar(1) null, --  Right acknowledges first Jack
  `AJL` varchar(1) null  --  Left acknowledges first Jack
);

alter table `Game` add (
  `OrganizerTricks` int null,
  `OpponentTricks` int null
);