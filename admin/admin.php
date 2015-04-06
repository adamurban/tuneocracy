<?php

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Tuneocracy - Admin Console</title>

<script src="/tuneocracy/jquery-2.1.3.min.js" type="text/javascript"></script>

<script type="text/javascript">

$(document).ready ( function () {

  $('#init_db').click(function() {    
    $.get( "initialize_db.php")
      .done(function( data ) {
      alert(data);
    });
  });

  $('#load_songs').click(function() {    
    $.get( "copy_playlist_into_db.php")
      .done(function( data ) {
      alert(data);
    });
  });

  $('#start_playback').click(function() {    
    $.get( "start_playback_engine.php")
      .done(function( data ) {
      alert(data);
    });
  });
  
  $('#stop_playback').click(function() {    
    $.get( "stop_playback_engine.php")
      .done(function( data ) {
      alert(data);
    });
  });
  
});
</script>


</head>

<body>
	<button id='init_db'>Initialize DB</button><br>
	<button id='load_songs'>Load Songs From iTunes</button><br>
	<button id='start_playback'>Start Playback</button><br>
	<button id='stop_playback'>Stop Playback</button><br>
</body>
</html>
