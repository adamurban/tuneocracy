tell application "iTunes"
	if (not (exists user playlist "Tuneocracy")) then
		make new user playlist with properties {name:"Tuneocracy"}
	end if
end tell