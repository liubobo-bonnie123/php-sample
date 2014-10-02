drop table if exists meta_default_items;

create table meta_default_items
(
    item_id int unsigned not null default '0',
    primary key (item_id)
);
