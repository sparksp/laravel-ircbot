<?php namespace IRC;

use Closure, Event;

/**
 * A command issued to the Bot
 *
 * @package  IRC
 * @category  Bundle
 * @author  Phill Sparks <me@phills.me.uk>
 * @copyright  2012 Phill Sparks
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */
class Command {

	/**
	 * @var Message
	 */
	protected $message;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var Sender
	 */
	protected $sender;

	/**
	 * @var array
	 */
	protected $params;

	/**
	 * Create a new command object
	 * 
	 * @param  Message $message
	 */
	function __construct(Message $message)
	{
		// Keep the original message
		$this->message = $message;
		// Keep the message sender handy
		$this->sender  = $message->sender;
		// Split the command name and params out
		list($this->name, $params) = explode(' ', $message->body, 2) + array("", "");
		
		if (substr($params, 0, 1) == ':')
		{
			$params = array(substr($params, 1));
		}
		else if (false !== $p = strpos($params, ' :'))
		{
			$text = substr($params, $p+2);
			$params = explode(' ', substr($params, 0, $p));
			$params[] = $text;
		}
		else
		{
			$params = explode(' ', $params);
		}
		$this->params = $params;
	}

	/**
	 * Create a new command
	 * 
	 * @param  Message $message
	 * @return Command
	 */
	public static function make(Message $message)
	{
		return new static($message);
	}

	/**
	 * Register a new command
	 * 
	 * @param  string $name
	 * @param  Closure $closure
	 */
	public static function register($name, Closure $closure)
	{
		Event::listen('irc::command: '.$name, $closure);
	}

	/**
	 * Run the command
	 */
	public function run()
	{
		$response = Event::fire('irc::command: '.$this->name, array($this));

		if (empty($response))
		{
			$response[] = Message::notice($this->sender->nick, "I don't know what you want me to do");
		}
		
		return $response;
	}

	/**
	 * Magic Getter
	 * 
	 * @param  string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'name':
			case 'sender':
			case 'params':
				return $this->$name;
			break;
		}
	}

	/**
	 * Magic Setter
	 * 
	 * @param  string $name
	 * @param  mixed  $value
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'name':
			case 'sender':
			case 'params':
				; // Read-only
			break;
		}
	}

	/**
	 * Magic Is Set Check
	 * 
	 * @param  string $name
	 * @return boolean
	 */
	public function __isset($name)
	{
		return isset($this->$name);
	}

	/**
	 * Magic Unsetter
	 * 
	 * @param  string $name
	 */
	public function __unset($name)
	{
		switch ($name)
		{
			case 'name':
			case 'sender':
			case 'params':
				; // Read-only
			break;
		}
	}

}
