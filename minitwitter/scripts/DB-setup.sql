CREATE TABLE tweet (
  tweet_id integer primary key auto_increment,
  account_id integer not null,
  message text not null,
  create_timestamp timestamp not null default CURRENT_TIMESTAMP
) Engine=InnoDB;

CREATE TABLE account (
  account_id integer primary key auto_increment,
  account_name tinytext not null,
  create_timestamp timestamp not null default CURRENT_TIMESTAMP
) Engine=InnoDB;

CREATE TABLE mention (
  mention_id integer primary key auto_increment,
  tweet_id integer not null,
  to_account_id integer not null,
  create_timestamp timestamp not null default CURRENT_TIMESTAMP
) Engine=InnoDB;

CREATE TABLE tweet_like (
  tweet_like_id integer primary key auto_increment,
  tweet_id integer not null,
  from_account_id integer not null,
  create_timestamp timestamp not null default CURRENT_TIMESTAMP
) Engine=InnoDB;