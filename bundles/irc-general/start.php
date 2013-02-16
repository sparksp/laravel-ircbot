<?php

/**
 * General responses to return
 *
 * @package  IRC-General
 * @category  Bundle
 * @author  Brian Retterer
 * @copyright  2013 Brian Retterer <bretterer@gmail.com>
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */

use IRC\Message;

$observer = function($message) {

	return array (
		listenFor($message, '!dataja', "Don't ask to ask, Just ask!"),
		listenFor($message, '!wysiwyg', "What you see is what you get")
	);
};

function listenFor($message, $trigger, $returnMessage) {
	$nick = strtolower($message->sender->nick);
	$body = end($message->params);
	$channel = $message->channel() ?: $message->sender->nick;

	$starterPosition = strpos($body, $trigger);

	if ($starterPosition !== false && $returnMessage != '')
	{
		if($starterPosition === 0) {
			$tell = null;
		}

		if($starterPosition > 0) {
			$bodyArr = explode(' ', $body);
			$tell = $bodyArr[0];
		}

		if($tell === null) {
			return Message::privmsg($channel, $returnMessage);
		} else {
			return Message::privmsg($channel, $tell . ": " . $returnMessage);
		}
	}
}

Message::listen('privmsg', $observer);
