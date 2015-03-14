<?php

$sessionid = htmlspecialchars($_COOKIE['tuneocracy_id']);

if (!$sessionid) {
  //If the user doesn't have a cookie yet, make one for them
  $randomstring = "tuneocracy" . rand() . time();
  $sessionid = md5('$randomstring'); //sessionid here is just a hash of the username + random number + time and a random salt
  setcookie("tuneocracy_id", $sessionid);
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

$sessionid = mysqli_real_escape_string($link, $sessionid);

$vote_string = htmlspecialchars($_GET["vote"]);

$vote_array = explode ("_" , $vote_string);

$vote_type = $vote_array[0];
$song_id = mysqli_real_escape_string($link, $vote_array[1]);

$vote_value = 0;

if ($vote_type == "upvote")
{
  $vote_value = 1;
}
else if ($vote_type == "downvote")
{
  $vote_value = -1;
}

$link->query("INSERT INTO `votes`
(`session_id`,
`song_id`,
`vote_value`)

VALUES

('$sessionid',
'$song_id',
'$vote_value')

ON DUPLICATE KEY
UPDATE vote_value = $vote_value;") or exit;

//now regenerate the table with the latest results from the db

$query = "SELECT sum(COALESCE(votes.`vote_value`,0)) as total_votes, currentPlaylist.`song_id`, currentPlaylist.`name`, currentPlaylist.`artist`, currentPlaylist.`playback_timestamp`
FROM `currentPlaylist`
LEFT JOIN `votes` on currentPlaylist.`song_id` = votes.`song_id`
WHERE `playback_timestamp` is NULL
GROUP BY `song_id`
ORDER BY  `total_votes` DESC";

$results = mysqli_query($link, $query);
$song_count = $results->num_rows;
?>

<thead>
<tr>
<th>Vote Total</th><th>Title</th><th>Artist</th>
</tr>
</thead>
<tbody>
<?php
//display all data from db in table form
while($row = mysqli_fetch_array($results))
{
  $upvote_id = "upvote_" . $row['song_id'];
  $downvote_id = "downvote_" . $row['song_id'];
  echo "<tr><td><div class='center'><img src='images/upvote_sm.png' alt='upvote' height='24' width='24' class='votebutton' id='$upvote_id'><br>"; 
  echo $row['total_votes'];
  echo "<br><img src='images/downvote_sm.png' alt='downvote' height='24' width='24' class='votebutton' id='$downvote_id'></div></td><td>";   
  echo $row['name'];
  echo "</td><td>";    
  echo $row['artist'];
  echo "</td></tr>\n";  
}
$link->close();
?>
<tbody>

