create table user (
	ID int not null auto_increment primary key,
	realname varchar(30) not null,
	username varchar(30) not null,
	password varchar(30) not null,
	height   int not null
) type innodb;


create table weighin (
	ID int not null auto_increment primary key,
	userID int not null,
	weight float not null,
	date timestamp not null default now(),
	index (userID),
	foreign key (userID) references user(ID)
) type innodb;

create table message (
	ID int not null auto_increment primary key,
	fromUserID int not null,
	toUserID int not null,
	message text not null,
	date timestamp not null default now(),
	foreign key(fromUserID) references user(ID),
	foreign key(toUserID) references user(ID),
	index(toUserID),
	index(fromUserID),
	index(date)
) type innodb;

