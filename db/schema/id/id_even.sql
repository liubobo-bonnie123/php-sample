drop table if exists ids_even;
create table ids_even
(
    id int auto_increment,
    primary key (id)
) engine = InnoDB;

insert into ids_even values ('NULL');
insert into ids_even values ('NULL');
