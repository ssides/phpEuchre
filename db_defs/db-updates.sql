
alter table `GamePlay` add (
  `CardFaceUp`  char(5) not null default '',
  `Dealer` varchar(1) not null
);

alter table `GamePlay` drop column `Alone`;
