create table user_band
(
    band_id int unsigned not null default '0',
    user_id int unsigned not null default '0',
    character_id int unsigned not null default '0',
    name varchar(192) not null default '0', 
    create_date int not null default '0',
    update_date int not null default '0',
    primary key (band_id),
    key k1 (user_id),
    unique key (user_id, character_id)
) charset=utf8;


