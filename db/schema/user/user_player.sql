create table user_player
(
    user_id int unsigned not null default '0',
    facebook_id varchar(30) not null,
    create_date int not null default '0',
    update_date int not null default '0',
    tickets int unsigned not null default '0',
    cash int unsigned not null default '0',
    locked int(1) not null default '0',
    logins int not null default '0',
    serial_id int unsigned not null default '0',
    category set ('player', 'developer', 'publisher', 'test') not null default 'player',
    deleted set ('Y','N') not null default 'N', 
    primary key (user_id),
    key k1 (facebook_id),
    key k2 (user_id, deleted)
);
