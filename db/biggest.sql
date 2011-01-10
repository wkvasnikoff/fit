create table user (
  ID int not null auto_increment primary key,
  realname varchar(30) not null,
  username varchar(30) not null,
  password varchar(30) not null
) type innodb;


create table weighin (
  ID int not null auto_increment primary key,
  userID int not null,
  weight int not null,
  date timestamp not null default now(),
  index (userID),
  foreign key (userID) references user(ID)
) type innodb;


