CREATE TABLE dtb_trackback (
	trackback_id int auto_increment NOT NULL,
	product_id int NOT NULL,
	blog_name varchar(255) NOT NULL DEFAULT '',
	title varchar(255) NOT NULL DEFAULT '',
	excerpt text NOT NULL ,
	url text NOT NULL ,
	status int NOT NULL DEFAULT 2,
	del_flg int NOT NULL DEFAULT 0,
	create_date datetime NOT NULL,
	update_date datetime NOT NULL,
	PRIMARY KEY (trackback_id)
) TYPE=InnoDB;
