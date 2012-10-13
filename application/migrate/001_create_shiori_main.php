<?php


$sql = 'CREATE TABLE `shiori_main`(
    `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `thema` VARCHAR(256) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `member` VARCHAR(256) NOT NULL,
    `detail` TEXT NOT NULL,
    `create_at` DATETIME NOT NULL,
    `update_at` DATETIME NOT NULL
)engine = InnoDB;';


