<?php 

return array(

	/**
	 * @var string Regex for matching usernames
	 */
	'regex'     => '/^(guest|qwebirc)/',

	 /**
	  * @var string Message to send to user when they join.  Placeholders are {{nick}} and {{channel}}.
	  */
	'response'  => 'Good morning {{nick}}, welcome to {{channel}}.  Please /nick so we know who you are.',

);
