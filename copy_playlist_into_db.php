<?php

function trim_whitespace(&$value) 
{ 
    $value = trim($value);
}

function create_nix_paths(&$string) 
{ 
  $string = rtrim($string); //trim any whitespace
  $string = rtrim($string, ','); //get rid of any trailing commas
  $string = str_replace(":", "/", $string); //change : delimited Applescript paths to / delimted *nix paths
  $string = strstr($string, '/'); //remove the volume identifier (everything before the first /)
}


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

//TODO: FIXME. This is a hideous hack... returned list is comma separated (yes, this means songs with a "," break things")
$songArray = explode(',', exec('osascript iTunesControl/GetSongNames.scpt'));
array_walk($songArray, 'trim_whitespace');
$artistArray = explode(',', exec('osascript iTunesControl/GetArtistNames.scpt'));
array_walk($artistArray, 'trim_whitespace');
$idArray = explode(',', exec('osascript iTunesControl/GetPersistentIDs.scpt'));
array_walk($idArray, 'trim_whitespace');
$pathArray = explode('alias ', exec('osascript iTunesControl/GetSongPaths.scpt'));
array_walk($pathArray, 'create_nix_paths');

//TODO: Fix the delimiter so we don't need to delete the first empty array entry:
if ($pathArray[0] == "")
{
  array_shift($pathArray);
}

//print_r ($pathArray);
//print_r ($artistArray);
//print_r ($songArray);
//print_r ($artistArray);

//Add every song from our iTunes playlist to our currentPlaylist mysql table
$totalSongs = count($songArray);

for ($i = 0; $i < $totalSongs; $i++)
{
  $id_value = mysqli_real_escape_string($link, $idArray[$i]);
  $song_value = mysqli_real_escape_string($link, $songArray[$i]);
  $artist_value = mysqli_real_escape_string($link, $artistArray[$i]);
  $path_value = mysqli_real_escape_string($link, $pathArray[$i]);

  $link->query("REPLACE INTO `currentPlaylist` (
    id,
    name,
    artist,
    filepath
    ) 

    VALUES(
    '$id_value',
    '$song_value',
    '$artist_value',
    '$path_value'
    )") or exit;
}

$link->close();

echo "Successfully copied playlist into DB";

?>