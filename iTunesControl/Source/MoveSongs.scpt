on run argv
	set inputArg to item 1 of argv
	
	tell application "iTunes"
		set myPlaylist to playlist "AppTest"
		move (some track of myPlaylist whose persistent ID is inputArg) to end of myPlaylist
	end tell
end run