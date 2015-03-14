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
  $query = "SELECT sum(COALESCE(votes.`vote_value`,0)) as total_votes, currentPlaylist.`song_id`, currentPlaylist.`name`, currentPlaylist.`artist`, currentPlaylist.`playback_timestamp`, currentPlaylist.`filepath`
  FROM `currentPlaylist`
  LEFT JOIN `votes` on currentPlaylist.`song_id` = votes.`song_id`
  WHERE `playback_timestamp` is NULL
  GROUP BY `song_id`
  ORDER BY  `total_votes` DESC
  LIMIT 1";

  $results = mysqli_query($link, $query);
  $row = mysqli_fetch_array($results);
  $song_path = $row['filepath'];
  $song_id = $row['song_id'];

  //mark the selected song's playback time as now
  $query = "UPDATE `currentPlaylist`
  SET `playback_timestamp` = CURRENT_TIMESTAMP
  WHERE `song_id` = '$song_id'";


  $results = mysqli_query($link, $query);

  //playback song here

  if ($song_path != "")
  {
    $song_path = escapeshellarg($song_path);

    echo "<br>Playing " . $song_path;
    //echo exec('afplay ' . $song_path);
    echo exec('afplay -t 20 ' . $song_path);
  }

  $link->close();
}

?>
