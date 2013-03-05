<?php

return array(

	/**
	 * The hostname of the IRC Server
	 * @var string
	 */
	'server' => 'irc.freenode.net',

	/**
	 * The port number of the IRC Server
	 * @var int
	 */
	'port' => 6667,

	/**
	 * The IRC user's username
	 * @var string
	 */
	'user' => 'miniroomie',

	/**
	 * The IRC user's nick
	 * @var string
	 */
	'nick' => 'miniroomie',

	/**
	 * The IRC user's name
	 * @var string
	 */
	'name' => 'Roomies Child',

	/**
	 * The password of the IRC Server
	 */
	// 'password' => '',

	/**
	 * A list of nicknames allowed to issue restricted commands.  Names must be in lowercase
	 * @var array
	 */
	'allow' => array(
		'lamarus_'
	),

	/**
	 * Settings for NickServ
	 * @var array
	 */
	'nickserv' => array(
		'nick'     => 'miniroomie',
		'password' => 'laravelminiroomie',
	),

	/**
	 * A list of channels to join once connected.
	 * @var array
	 */
	'join' => array(
		'#laravel-bot-test'
	),

	/**
	 * Enable very verbose logging.
	 * @var boolean
	 */
	'debug' => false,

	/**
	 * The logging function
	 * @param IRC\Message $message
	 * @param bool $sent TRUE if the client sent this message
	 */
	'logger' => function(IRC\Message $message, $sent = false)
	{
		$file = $message->target();
		if (is_null($file))
		{
			$file == Config::get('irc::client.server');
		}
		else if ($file == Config::get('irc::client.nick'))
		{
			$file = '@'.$message->sender->nick;
		}
		else if ($file[0] != '#' and $file[0] != '&')
		{
			$file = '@'.$file;
		}

		File::append(path('storage').'logs/irc/'.strtolower($file).'.log', date('Y-m-d H:i:s').' - '.$message->raw."\r\n");

		if ($sent)
		{
			$output = ANSI::bold($message->raw);
		}
		else if ($message->isError())
		{
			$output = ANSI::color(ANSI::RED, false, $message->raw);
		}
		else
		{
			$output = str_replace(Config::get('irc::client.nick'), ANSI::color(ANSI::GREEN, false, Config::get('irc::client.nick')), $message->raw);
		}

		echo "$output\n";
		ob_flush(); flush();
	},

);
