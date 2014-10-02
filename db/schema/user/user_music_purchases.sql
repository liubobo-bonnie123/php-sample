create table user_music_purchases
(
    user_id int unsigned not null default '0', 
    song_id int unsigned not null default '0',
    order_id varchar(32) not null,
    price decimal(4,2) not null, 
    tax decimal(4,2) not null, 
    items int not null default '0',
    create_date int not null default '0',
    primary key (user_id, song_id),
    key k1 (user_id),
    key k2 (song_id)
);
