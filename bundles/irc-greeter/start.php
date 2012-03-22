<?php

/**
 * Greets people as they join a channel the bot is in.
 * 
 * @package  IRC-Greeter
 * @category  Bundle
 * @author  Phill Sparks <me@phills.me.uk>
 * @copyright  2012 Phill Sparks
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */

use IRC\Message;

// List of users to greet, in lowercase
$greet = array(
	'daylerees',
	'ericbarnes',
	'jasonlewis',
	'ianlandsman',
	'phillsparks',
	'shawnmccool',
	'taylorotwell',
);

$greeter = function($message) use ($greet)
{
	$nick = strtolower($message->sender->nick);
	if (in_array($nick, $greet))
	{
		// Use the cache to prevent re-greeting more than once per hour,
		// useful if someone has connection issues or if they get host-cloaked
		// by the server
		$key = 'irc-greeted-'.$nick;
		if ( ! Cache::has($key))
		{
			Cache::put($key, mktime(), 60); // 1 hour

			return Message::privmsg(end($message->params), 'Morning '.$message->sender->nick.'!');
		}
	}
};

Message::listen('join', $greeter);
Message::listen('nick', $greeter);
