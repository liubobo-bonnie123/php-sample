create table cs_refund
(
    order_id bigint unsigned not null default '0',
    facebook_status varchar(30) not null,
    refund_status set ('Y','N') not null default 'N',
    primary key (order_id)
) charset=utf8;