create table user_xp
(
    user_id int unsigned not null default '0',
    character_id int unsigned not null default '0',
    xp_type enum ('role', 'game', 'overall'),
    xp_subtype int unsigned not null default '0',
    xp bigint unsigned not null default '0',
    primary key (user_id, character_id, xp_type, xp_subtype)
);
