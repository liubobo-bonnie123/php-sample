create table user_character
(
    character_id int unsigned not null default '0',
    user_id int unsigned not null default '0',
    create_date int not null default '0',
    update_date int not null default '0',
    genre_id int unsigned not null default '0',
    avatar_id int unsigned not null default '0',
    garage_id int unsigned not null default '0',
    producer_id int unsigned not null default '0',
    level int unsigned not null default '0',
    wattz int unsigned not null default '0',
    name varchar(192) character set utf8 not null default '',
    deleted set ('Y','N') not null default 'N',
    primary key (character_id),
    key k1 (user_id),
    key k2 (deleted)
);
