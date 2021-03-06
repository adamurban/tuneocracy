<?php
$sessionid = htmlspecialchars($_COOKIE['tuneocracy_id']);

if (!$sessionid) {
  //If the user doesn't have a cookie yet, make one for them
  $randomstring = rand() . "tuneocracy" . time();
  $sessionid = md5($randomstring); //sessionid here is just a hash of the username + random number + time and a random salt
  setcookie("tuneocracy_id", $sessionid);
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Tuneocracy</title>

<script src="jquery-2.1.3.min.js" type="text/javascript"></script>

<script>

(function ( $ ) { 
    $.fn.bindimages = function() {
        $(".votebutton").click(function(){
          $.get("vote.php", {vote: event.target.id})
            .done(function( data ) {
              $("#unplayedSongTable")
              .empty()
              .append(data)
              .bindimages();
            });
          });
        return this;
    };
 
}( jQuery ));


$(document).ready(function(){
    setInterval(function() {
    $.get( "get_latest_data.php")
            .done(function( data ) {
                document.getElementById("song_tables").innerHTML = data;
                $(document).bindimages();
        });
    }, 2000);
});
</script>

<style>
.center {
    text-align: center;
}
tr:nth-child(even) {background: #CCC}
tr:nth-child(odd) {background: #FFF}
table {
    border-collapse: collapse;
    width: 80%;
}

table, th, td {
    border: 1px solid black;
    min-width:100px;
}

</style>

</head>

<body>

<?php
//the click binding above goes away after the empty... fix it
$user = 'root';
$password = 'root';
$db = 'tuneocracy';
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

$query = "SELECT sum(COALESCE(votes.`vote_value`,0)) as total_votes, currentPlaylist.`song_id`, currentPlaylist.`name`, currentPlaylist.`artist`, currentPlaylist.`playback_timestamp`
FROM `currentPlaylist`
LEFT JOIN `votes` on currentPlaylist.`song_id` = votes.`song_id`
WHERE `playback_timestamp` is not NULL
GROUP BY `song_id`
ORDER BY  `playback_timestamp` ASC";

$results = mysqli_query($link, $query);

?>

<!--Replace everything in song_tables div via ajax every 2s-->
<div id="song_tables">
Previous Songs:
<table id="playedSongTable">
<thead>
<tr>
<th>Vote Total</th><th>Title</th><th>Artist</th><th>Playback Time</th>
</tr>
</thead>
<tbody>
<?php
$last_song_num = $results->num_rows;
$i = 1;

while($row = mysqli_fetch_array($results))
{
  echo "<tr><td>"; 
  if ($i == $last_song_num)
  {
    echo "Now Playing";
  }  
  else
  {
   echo $row['total_votes'];
  }
  echo "</td><td>";   
  echo $row['name'];
  echo "</td><td>";    
  echo $row['artist'];
  echo "</td><td>";
  echo $row['playback_timestamp'];
  echo "</td></tr>\n"; 
  $i++; 
}
?>
<tbody>
</table>
<br>
Vote on Upcoming Songs:
<?php
$query = "SELECT sum(COALESCE(votes.`vote_value`,0)) as total_votes, currentPlaylist.`song_id`, currentPlaylist.`name`, currentPlaylist.`artist`, currentPlaylist.`playback_timestamp`
FROM `currentPlaylist`
LEFT JOIN `votes` on currentPlaylist.`song_id` = votes.`song_id`
WHERE `playback_timestamp` is NULL
GROUP BY `song_id`
ORDER BY  `total_votes` DESC";

$results = mysqli_query($link, $query);
$song_count = $results->num_rows;
?>

<table id="unplayedSongTable">
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
</table>
</div>
</body>
</html>
