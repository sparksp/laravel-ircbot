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
	'ericbarnes',
	'jasonlewis',
	'phillsparks',
	'taylorotwell',
);

// Listen to JOIN messages
Message::listen('join', function($message) use ($greet)
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
});

// When Rommie joins a room she'll get a list of who's in the room, check
// through here for people to greet.
Message::listen(Message::RPL_NAMREPLY, function($message) use ($greet)
{
	$params  = $message->params;
	$names   = 
		explode(' ',                   // Get them as an array
			str_replace(' @', ' ',     // Remove the OP sign
				strtolower(            // Compare lowercase names
					array_pop($params)
				)
			)
		);
	$channel = array_pop($params);

	// Get the names we want to greet
	$names   = array_intersect($names, $greet);
	if (count($names))
	{
		return Message::privmsg($channel, 'Morning '.implode(', ', $names).'!');
	}
});
