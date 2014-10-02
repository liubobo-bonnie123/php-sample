create table user_facebook
(
    facebook_id varchar(30) not null,
    first_name varchar(64) not null default '',
    primary key (facebook_id)
);
