create table user_band_member
(
    band_id int unsigned not null default '0',
    user_id int unsigned not null default '0',
    facebook_id varchar(30) not null,
    member_id int unsigned not null default '0',
    member_type enum ('player', 'artist', 'npc', 'placeholder'),
    slot_id int unsigned not null default '0',
    create_date int not null default '0',
    update_date int not null default '0',
    primary key (band_id, user_id, slot_id, member_id),
    key k1 (facebook_id)
);
