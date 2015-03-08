<?php
function trim_value(&$value) 
{ 
    $value = trim($value); 
}

//TODO: FIXME. This is a hideous hack... returned list is comma separated (yes, this means songs with a "," break things")
$songArray = explode(',', exec('osascript iTunesControl/GetSongNames.scpt'));
array_walk($songArray, 'trim_value');
$artistArray = explode(',', exec('osascript iTunesControl/GetArtistNames.scpt'));
array_walk($artistArray, 'trim_value');
$idArray = explode(',', exec('osascript iTunesControl/GetPersistentIDs.scpt'));
array_walk($idArray, 'trim_value');

//print_r ($songArray);
//print_r ($artistArray);

$user = 'root';
$password = 'root';
$db = 'testapp';
$host = 'localhost';
$port = 3306;

$link = mysqli_init();
$success = mysqli_real_connect(
   $link, 
   $host, 
   $user, 
   $password, 
   $db,
   $port
);

//Add every song from our iTunes playlist to our currentPlaylist mysql table
$totalSongs = count($songArray);

for ($i = 0; $i < $totalSongs; $i++) {
  $link->query("REPLACE INTO `currentPlaylist` (
    id,
    name,
    artist
    ) 

    VALUES(
    '$idArray[$i]',
    '$songArray[$i]',
    '$artistArray[$i]'
    )") or exit;
}

$link->close();

?>