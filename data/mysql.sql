CREATE TABLE `user` (
`id`  int(11) NOT NULL ,
`username`  varchar(20) NOT NULL ,
`password`  varchar(150) NOT NULL ,
`email`  varchar(50) NOT NULL DEFAULT '' ,
`mobile`  varchar(30) NOT NULL DEFAULT '' ,
`first_name`  varchar(20) NOT NULL DEFAULT '' ,
`last_name`  varchar(20) NOT NULL DEFAULT '' ,
`avatar`  varchar(100) NOT NULL DEFAULT '' ,
`sex`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0未知，1男，2女' ,
`deleted`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
`created_at`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
`update_at`  int(11) NOT NULL DEFAULT 0 ,
`last_login_ip`  char(15) NOT NULL DEFAULT '' ,
`last_login_time`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
`reg_ip`  char(15) NOT NULL DEFAULT '' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_general_ci
;

