drop table if exists meta_item_index;

create table meta_item_index
(
    item_id int unsigned not null default '0',
    tablename varchar(96) not null default '',
    primary key (item_id)
);
