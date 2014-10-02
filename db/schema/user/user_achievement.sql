create table user_achievement
(
    user_id int unsigned not null default '0',
    character_id int unsigned not null default '0',
    achievement_id int unsigned not null default '0',
    step bigint not null default '0',
    create_date int not null default '0',
    primary key (user_id, character_id, achievement_id)
);
