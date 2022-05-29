create table tx_bwtodosite_domain_model_profile (
	uid     int(11) unsigned default 0 not null auto_increment,
	pid     int(11) default 0 not null,

	name    varchar(255) not null,
	tasks   int(11) unsigned default 0 not null,

	tstamp  int(11) unsigned default 0 not null,
	crdate  int(11) unsigned default 0 not null,
	deleted tinyint(4) unsigned default 0 not null,
	hidden  tinyint(4) unsigned default 0 not null,

	primary key (uid),
	KEY     parent (pid),
);

create table tx_bwtodosite_domain_model_task (
	uid         int(11) unsigned default 0 not null auto_increment,
	pid         int(11) default 0 not null,

	title       varchar(255) not null,
	description text,
	due_date    int(11) unsigned default 0 not null,
	profile     int(11) unsigned default 0 not null,

	tstamp      int(11) unsigned default 0 not null,
	crdate      int(11) unsigned default 0 not null,
	deleted     tinyint(4) unsigned default 0 not null,
	hidden      tinyint(4) unsigned default 0 not null,

	primary key (uid),
	KEY         parent (pid),
);
