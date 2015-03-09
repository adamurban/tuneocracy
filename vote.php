<?php

$vote_string = htmlspecialchars($_GET["vote"]);

$vote_array = explode ("_" , $vote_string);

$vote_type = $vote_array[0];
$vote_id = $vote_array[1];

$vote_value = 0;

if ($vote_type == "upvote")
{
  $vote_value = 1;
}
else if ($vote_type == "downvote")
{
  $vote_value = -1;
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

$link->query("UPDATE `currentPlaylist` 
  SET voteTotal = voteTotal + $vote_value
  WHERE `id` = '$vote_id'"
  ) or exit;

//now regenerate the table with the latest results from the db
?>
<thead>
<tr>
<th>Vote Total</th><th>Title</th><th>Artist</th><th>id</th>
</tr>
</thead>
<tbody>
<?php
$query = "SELECT `voteTotal`,`name` , `artist`, `id`
FROM `currentPlaylist`
WHERE `playback_timestamp` is NULL
ORDER BY  `voteTotal` DESC";

$results = mysqli_query($link, $query);
while($row = mysqli_fetch_array($results))
{
  $persistent_id = $row['id'];
  $upvote_id = "upvote_" . $persistent_id;
  $downvote_id = "downvote_" . $persistent_id;
  echo "<tr><td><div class='center'><img src='images/upvote_sm.png' alt='upvote' height='24' width='24' class='votebutton' id='$upvote_id'><br>"; 
  echo $row['voteTotal'];
  echo "<br><img src='images/downvote_sm.png' alt='downvote' height='24' width='24' class='votebutton' id='$downvote_id'></div></td><td>";   
  echo $row['name'];
  echo "</td><td>";    
  echo $row['artist'];
  echo "</td><td>";
  echo $persistent_id;
  echo "</td></tr>\n";
  
  //reorder the actual iTunes playlist
  exec("osascript iTunesControl/MoveSongs.scpt " . $persistent_id);
  
}
$link->close();
?>
<tbody>

