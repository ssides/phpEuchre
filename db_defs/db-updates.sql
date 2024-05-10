ALTER TABLE `Game`
  ADD `Speed` int not null default 0; -- Speed of game. Use 0 for slow, 1 for fast
  
ALTER TABLE `Game`
  ADD `GameEndDate` datetime null default null; -- Date game was ended by the organizer.