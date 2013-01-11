<?php

/**
 * Responds to '!ugt [nick to highlight]' with a link to the UGT convention
 *
 * @package  IRC-UGT
 * @category  Bundle
 * @author  Jérôme Foray <meroje.f@gmail.com>
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */

use IRC\Message;

// Rommie will watch out for people saying '!ugt [nick to highlight]'
$observer = function($message)
{
	$nickToHighlight = strtolower($message->sender->nick);
	$body = end($message->params);
	$starter = "!ugt";

	if (starts_with($body, $starter))
	{
		//grab the nick to be highlighted
		$nick = trim(substr($body, strlen($starter)));
		//if the nick is a valid string,
		if ($nick !== '')
		{
			$nickToHighlight = $nick;
		}

		$channel = $message->channel() ?: $message->sender->nick;
		return Message::privmsg($channel, $nickToHighlight . ": It is always morning when someone comes into a channel. We call that Universal Greeting Time http://www.total-knowledge.com/~ilya/mips/ugt.html");
	}
};
Message::listen('privmsg', $observer);
