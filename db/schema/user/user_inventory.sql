create table user_inventory
(
    user_id int unsigned not null default '0', 
    character_id int unsigned not null default '0',
    item_id int unsigned not null default '0',
    quantity int not null default '0',
    equipped enum('Y','N') not null default 'N',
    song_id int unsigned not null default '0', 
    primary key (user_id, character_id, item_id, song_id)
);
