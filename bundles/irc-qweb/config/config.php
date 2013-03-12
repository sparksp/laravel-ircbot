<?php 

return array(
	'timeDelay'		=> 0, // How long between notices from bot should there be in seconds
	'regex'		=> '/^(guest|qwebirc)/', // Regex for matching usernames
	'response'		=> 'Good morning {{nick}}, welcome to {{channel}}.  Please /nick so we know who you are.' // Message to send to user when they join.
);