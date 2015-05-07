<?php 

return array(

	/**
	 * @var string Regex for matching usernames
	 */
	'regex'     => '/^(guest|laravelnewbie|qwebirc)/',

	 /**
	  * @var string Message to send to user when they join.  Placeholders are {{nick}} and {{channel}}.
	  */
	'response'  => 'Good morning {{nick}}, welcome to {{channel}}.  Please type in "/nick your_new_nick" to change your name so we can distinguish you easily.',

);
