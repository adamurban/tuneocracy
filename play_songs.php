<?php
//Loop forever for now... This is our payback engine. Eventually add a break so the user can quit ;-)

while(1)
{

  if(connection_status() != CONNECTION_NORMAL)
  {
    break; //for now, kill our run loop if the page is closed. this will wait until the current song finishes
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

  //select highest voted song that hasn't been played yet
  $query = "SELECT `filepath`,`voteTotal`,`id`
  FROM `currentPlaylist`
  WHERE `playback_timestamp` is NULL
  ORDER BY  `voteTotal` DESC
  LIMIT 1";

  $results = mysqli_query($link, $query);
  $row = mysqli_fetch_array($results);
  $song_path = $row['filepath'];
  $song_id = $row['id'];

  //mark the selected song's playback time as now
  $query = "UPDATE `currentPlaylist`
  SET `playback_timestamp` = CURRENT_TIMESTAMP
  WHERE `id` = '$song_id'";


  $results = mysqli_query($link, $query);

  //playback song here

  if ($song_path != "")
  {
    $song_path = escapeshellarg($song_path);

    echo "<br>Playing " . $song_path;
    echo exec('afplay ' . $song_path);
    //echo exec('afplay -t 20 ' . $song_path);
  }

  $link->close();
}

?>
