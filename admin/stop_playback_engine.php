<?php
$lock_file_name = 'playback_lock';
$full_lock_path = getcwd() . "/" . $lock_file_name;

if (file_exists($full_lock_path)) {
    $pid = file_get_contents($lock_file_name);
    exec('kill ' . $pid);
    exec('killall afplay');
    unlink($lock_file_name);
    echo "Stopped Playback Engine";
} else
{
    echo "Engine already stopped";
}
?>
