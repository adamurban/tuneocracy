<?php

exec('osascript iTunesControl/CreatePlaylist.scpt');

$user = 'root';
$password = 'root';
$db = 'tuneocracy';
$host = 'localhost';

$link = mysqli_init();
$success = mysqli_real_connect(
   $link, 
   $host, 
   $user, 
   $password
);

$link->query("
    DROP DATABASE IF EXISTS `tuneocracy`
  ") or exit;

$link->query("
    CREATE DATABASE `tuneocracy`
  ") or exit;
  
$link->close();


$link = mysqli_init();
$success = mysqli_real_connect(
   $link, 
   $host, 
   $user, 
   $password, 
   $db,
   $port
);

$link->query("
    DROP TABLE IF EXISTS `votes`
  ") or exit;
  
$link->query("
    DROP TABLE IF EXISTS `currentPlaylist`
  ") or exit;

$link->query("
    CREATE TABLE `currentPlaylist` (
    `song_id` varchar(16) NOT NULL,
    `name` text NOT NULL,
    `artist` text NOT NULL,
    `filepath` text NOT NULL,
    `playback_timestamp` timestamp NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  ") or exit;
  
$link->query("
    ALTER TABLE `currentPlaylist`
    ADD PRIMARY KEY (`song_id`),
    ADD UNIQUE KEY `id` (`song_id`)
  ") or exit;
  
$link->query("
    CREATE TABLE `votes` (
    `session_id` varchar(128) NOT NULL,
    `song_id` varchar(128) NOT NULL,
    `vote_value` int(11) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  ") or exit;
  
$link->query("
    ALTER TABLE `votes`
    ADD PRIMARY KEY (`session_id`,`song_id`);
  ") or exit;  
  


$link->close();

echo "Successfully initialized DB";

?>