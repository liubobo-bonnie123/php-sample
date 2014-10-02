drop table if exists meta_info;

create table meta_info
(
    create_date datetime not null
);

insert into meta_info values (now());
