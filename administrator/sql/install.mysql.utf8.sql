CREATE TABLE IF NOT EXISTS `#__attlist_item` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`catid` TEXT NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
`present` VARCHAR(255)  NOT NULL ,
`event_date` DATE NOT NULL ,
`event_title` VARCHAR(255)  NOT NULL ,
`creation_date` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`note` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

