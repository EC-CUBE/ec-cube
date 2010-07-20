CREATE TABLE dtb_trackback (
	trackback_id serial primary key NOT NULL,
	product_id int NOT NULL,
	blog_name varchar(255) NOT NULL DEFAULT '',
	title varchar(255) NOT NULL DEFAULT '',
	excerpt text NOT NULL DEFAULT '',
	url text NOT NULL DEFAULT '',
	status int2 NOT NULL DEFAULT 2,
	del_flg int2 NOT NULL DEFAULT 0,
	create_date timestamp NOT NULL,
	update_date timestamp NOT NULL
);
