<?php

/**
 * Core commands
 * 
 * @package  IRC
 * @category  Bundle
 * @author  Phill Sparks <me@phills.me.uk>
 * @copyright  2012 Phill Sparks
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */

use IRC\Command, IRC\Message;

Command::register('help', function($command)
{
	return Message::privmsg($command->sender->nick, 'Yeah, you need help!');
});

Command::register('join', function($command)
{
	list($channel, $key) = $command->params;
	return Message::join($channel, $key);
});

Command::register('part', function($command)
{
	$message = $command->params;
	$channel = array_shift($message);
	$message = implode(' ', $message);
	return Message::part($channel, $message);
});

Command::register('quit', function($command)
{
	echo 'quit', PHP_EOL;

	$message = implode(' ', $command->params);
	return array(
		Message::notice($command->sender->nick, 'Going offline now'),
		Message::quit($message),
	);
});

Command::register('say', function($command)
{
	$message = $command->params;
	$receiver = array_shift($message);
	$message = implode(' ', $message);

	return Message::privmsg($receiver, $message);
});

Command::register('notice', function($command)
{
	$message = $command->params;
	$receiver = array_shift($message);
	$message = implode(' ', $message);

	return Message::notice($receiver, $message);
});

Command::register('act', function($command)
{
	$message = $command->params;
	$receiver = array_shift($message);
	$message = implode(' ', $message);

	return Message::privmsg($receiver, "\x01ACTION $message\x01");
});

Command::register('echo', function($command)
{
	$params = $command->params;
	$name   = array_shift($params);
	return Message::make($name, $params);
});


/**
 * Very verbose debugging.
 */
if (Config::get('irc::client.debug'))
{
	Message::listen('privmsg', function($message)
	{
		if ( ! $message->sender)
		{
			echo ANSI::color(ANSI::BLUE, false, "[DEBUG] SENDER: ") . "None\n";
		}
		else if ($message->sender->isServer())
		{
			echo ANSI::color(ANSI::BLUE, false, "[DEBUG] SERVER: ") . $message->sender->server ."\n";
		}
		else if ($message->sender->isUser())
		{
			echo ANSI::color(ANSI::BLUE, false, "[DEBUG] NICK: ") . $message->sender->nick ."\n";
		}
		// Command
		{
			echo ANSI::color(ANSI::BLUE, false, "[DEBUG] COMMAND: ") . $message->command ."\n";
		}
		foreach ($message->params as $n => $p)
		{
			echo ANSI::color(ANSI::BLUE, false, "[DEBUG] PARAM[$n]: ") . $message->params[$n] ."\n";
		}
	});
}