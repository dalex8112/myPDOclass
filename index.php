<?php

require_once dirname(__FILE__) .'/pdo/PDOClass.php';

$db = new PDOClass();

$sql = "DROP TABLE IF EXISTS `users`;";
$db->query($sql)->execute();

$sql = "CREATE TABLE `users` (
    `id`          int(5)      NOT NULL AUTO_INCREMENT,
    `firstname`   varchar(50) DEFAULT NULL,
    `surname`     varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`)
);";
$db->query($sql)->execute();

$sql = "INSERT INTO `users` VALUES
    (NULL, 'Joe',  'Bloggs'),
    (NULL, 'John', 'Doe'),
    (NULL, 'Jane', 'Doe');";
$db->query($sql)->execute();

// Poner aquí codigo en el fichero de ejemplo