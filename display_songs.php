<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>TestApp</title>

<script src="jquery-2.1.3.min.js" type="text/javascript"></script>

<script>

(function ( $ ) { 
    $.fn.bindimages = function() {
        $(".votebutton").click(function(){
          $.get("vote.php", {vote: event.target.id})
            .done(function( data ) {
              $("#songTable")
              .empty()
              .append(data)
              .bindimages();
            });
          });
        return this;
    };
 
}( jQuery ));


$(document).ready(function(){
    $(document).bindimages();
});
</script>

<style>
.center {
    text-align: center;
}
</style>

</head>

<body>

<?php
//the click binding above goes away after the empty... fix it
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

$query = "SELECT `voteTotal`,`name` , `artist`, `id`
FROM `currentPlaylist`
ORDER BY  `voteTotal` DESC";

$results = mysqli_query($link, $query);
$song_count = $results->num_rows;
?>

<table id="songTable">
<thead>
<tr>
<th>Vote Total</th><th>Title</th><th>Artist</th><th>id</th>
</tr>
</thead>
<tbody>
<?php
//display all data from db in table form
while($row = mysqli_fetch_array($results))
{
  $upvote_id = "upvote_" . $row['id'];
  $downvote_id = "downvote_" . $row['id'];
  echo "<tr><td><div class='center'><img src='images/upvote_sm.png' alt='upvote' height='24' width='24' class='votebutton' id='$upvote_id'><br>"; 
  echo $row['voteTotal'];
  echo "<br><img src='images/downvote_sm.png' alt='downvote' height='24' width='24' class='votebutton' id='$downvote_id'></div></td><td>";   
  echo $row['name'];
  echo "</td><td>";    
  echo $row['artist'];
  echo "</td><td>";
  echo $row['id'];
  echo "</td></tr>\n";  
}
$link->close();
?>
<tbody>
</table>
</body>
</html>
