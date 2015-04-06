<?php

$full_php_path = PHP_BINDIR . "/php";

$lock_file_name = 'playback_lock';
$full_lock_path = getcwd() . "/" . $lock_file_name;

if (file_exists($full_lock_path)) {
    $pid = file_get_contents($lock_file_name);
    echo "Playback Engine already running with PID " . $pid;
} else {
    $pid = exec("nohup $full_php_path play_songs.php > /dev/null 2>&1 & echo $!");
    file_put_contents($lock_file_name, $pid);
    echo "Started Playback Engine, PID " . $pid;
}
?>