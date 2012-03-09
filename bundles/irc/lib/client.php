<?php namespace IRC;

use Config, Event, Socket, ANSI;

/**
 * IRC Client
 *
 * An IRC Client connects to IRC Servers and handles client communication
 *
 * @package  IRC
 * @category  Bundle
 * @author  Phill Sparks <me@phills.me.uk>
 * @copyright  2012 Phill Sparks
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */
class Client {

	/**
	 * The socket connection to the IRC Server
	 *
	 * @var Socket
	 */
	private $socket = null;

	/**
	 * The client sender
	 *
	 * @var IRC\Sender
	 */
	protected $sender = null;

	/**
	 * Connect to the server and log in
	 */
	public function __construct()
	{
		echo ANSI::color(ANSI::CYAN, false,
			'CONNECT '. Config::get('irc::client.server') .':'. Config::get('irc::client.port', 6667)
		), "\n";

		/**
		 * @todo Use IoC to create the socket
		 */
		$this->socket = new Socket(Config::get('irc::client.server'), Config::get('irc::client.port', 6667));
		$this->login();

		foreach (Config::get('irc::client.join', array()) as $channel)
		{
			Message::join($channel)
				->send($this->socket);
		}

		Message::notice('PhillSparks', "I am online")->send($this->socket);

		$this->main();

		$this->socket = null; // Close socket
	}

	/**
	 * Log in to the server
	 */
	protected function login()
	{
		if (Config::get('irc::client.password'))
		{
			// TODO: Implement PASS message
			// $this->pass(Config::get('irc::client.password'));
		}
		$this->sender = Sender::makeUser(Config::get('irc::client.nick'));
		Message::user(Config::get('irc::client.user'), Config::get('irc::client.name'))->send($this->socket);
		Message::nick(Config::get('irc::client.nick'))->send($this->socket);

		// Wait for the end of the MOTD
		while ($message = $this->read())
		{
			if ($message->command == Message::RPL_ENDOFMOTD)
			{
				break;
			}
		}

		// Identify with NickServ
		if (Config::get('irc::client.nickserv.password'))
		{
			Message::privmsg('NickServ', 'IDENTIFY '. Config::get('irc::client.nickserv.nick', Config::get('irc::client.nick')) .' '. Config::get('irc::client.nickserv.password'))
				->send($this->socket);
		}

		// Request whois information about ourself
		Message::whois(Config::get('irc::client.nick'))->send($this->socket);
	}

	/**
	 * Store a message to the log.
	 *
	 * @param IRC\Message $message
	 * @param bool $sent TRUE if the client sent this message
	 */
	public static function log(Message $message, $sent = false)
	{
		if ($logger = Config::get('irc::client.logger'))
		{
			$logger($message, $sent);
		}
	}

	/**
	 * read
	 *
	 * @return IRC\Message
	 */
	protected function read()
	{
		do
		{
			if ($message = Message::parse($this->socket->read()))
			{
				// Log every message recieved
				Client::log($message);

				// Handle some core commands
				switch ($message->command)
				{
					case 'ERROR':
						return; // finish
					break;

					case 'PING':
						Message::pong($message->params[0])->send($this->socket);
					break;
				}
				return $message;
			}
			if ($this->socket->eof()) return false;
		}
		while ( ! $messsage);
	}

	/**
	 * main
	 *
	 * The main method is the work horse of the client, it loops through all the 
	 * messages recieved until an ERROR is returned.
	 */
	protected function main()
	{
		while ($message = $this->read())
		{
			Message::sendArray(array_merge(
				Event::fire('irc::message: '.strtolower($message->command), array($message)),
				Event::fire('irc::message: *', array($message))
			), $this->socket);

			if ($message->command == Message::RPL_WHOISUSER)
			{
				if (strcasecmp($message->params[1], $this->sender->nick) == 0)
				{
					$this->sender = Sender::makeUser($message->params[1], $message->params[2], $message->params[3]);
				}
			}

			else if ($message->command == 'INVITE')
			{
				if (in_array($message->sender->nick, Config::get('irc::client.allow')))
				{
					Message::join($message->params[1])->send($this->socket);
				}
				else
				{
					Message::notice($message->sender->nick, "Daddy always told me not to go with strangers")->send($this->socket);
				}
			}

			// Respond to private messages from trusted users
			else if ($message->command == 'PRIVMSG' and $message->params[0] == $this->sender->nick)
			{
				// TODO: Use host mask instead of "nick"
				if (in_array($message->sender->nick, Config::get('irc::client.allow')))
				{
					Message::sendArray(
						Command::make($message)->run(), $this->socket
					);
				}
				else
				{
					Message::notice($message->sender->nick, "Daddy always told me not to talk to strangers")->send($this->socket);
				}
			}

		}
	}

}

