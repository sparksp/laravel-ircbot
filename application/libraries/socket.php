<?php

/**
 * Socket
 *
 * A simple wrapper to handle reading to and writing from a socket.  Most socket commands can be chained together...
 *
 * <code>
 *     Socket::make('example.com', 8080)
 *         ->set_timeout(1000)
 *         ->write("Hello\r\n")
 *         ->read();
 * </code>
 *
 * @author Phill Sparks <me@phills.me.uk>
 * @version 1.0
 */
class Socket {

	/**
	 * The actual PHP socket
	 *
	 * @var resource
	 */
	private $socket = null;

	/**
	 * The hostname of the connection
	 *
	 * If OpenSSL support is installed, you may prefix the hostname with
	 * either ssl:// or tls:// to use an SSL or TLS client connection over
	 * TCP/IP to connect to the remote host.
	 *
	 * @var string
	 */
	private $hostname;

	/**
	 * The port of the connection
	 *
	 * @var int
	 */
	private $port;

	/**
	 * The connection timeout, in seconds
	 *
	 * @var int
	 */
	private $timeout = null;

	/**
	 * The number of bytes written by the last {@link write()}
	 *
	 * @var int
	 */
	public $written = 0;

	/**
	 * Create a new socket
	 *
	 * @param string $hostname The host name.
	 * @param int $port The port number.
	 * @param int $timeout The connection timeout, in seconds.
	 */
	public function __construct($hostname, $port, $timeout = null)
	{
		$this->hostname = $hostname;
		$this->port = $port;
		$this->timeout = is_null($timeout) ? ini_get("default_socket_timeout") : $timeout;

		$this->connect();
	}

	/**
	 * Create a new socket
	 *
	 * @param string $hostname The host name.
	 * @param int $port The port number.
	 * @param int $timeout The connection timeout, in seconds.
	 * @return Socket
	 */
	public static function make($hostname, $port, $timeout = null)
	{
		return new static($hostname, $port, $timeout);
	}

	/**
	 * Connect the socket, has no effect if the socket is already connected.
	 *
	 * @return Socket
	 */
	public function connect()
	{
		if (is_null($this->socket))
		{
			$this->socket = fsockopen($this->hostname, $this->port, $this->timeout);
		}
		return $this;
	}

	/**
	 * Set a timeout for reading/writing data over the socket
	 *
	 * Sets the timeout value on the stream, expressed in the sum of seconds and microseconds.
	 *
	 * @param int $seconds The seconds part of the timeout to be set.
	 * @param int $microseconds The microseconds part of the timeout to be set.
	 * @return Socket
	 */
	public function set_timeout($seconds, $microseconds = 0)
	{
		$this->connect();
		stream_set_timeout($this->socket, $seconds, $microseconds);
		return $this;
	}

	/**
	 * Read a line from the socket
	 *
	 * @param int $length Reading ends when length - 1 bytes have been read, on a newline (which is included in the return value), or on EOF (whichever comes first). If no length is specified, it will keep reading from the stream until it reaches the end of the line.
	 * @return string|FALSE Returns a string of up to length - 1 bytes read from the file pointed to by handle. If there is no more data to read in the file pointer, then FALSE is returned.
	 */
	public function read($length = 4096)
	{
		$this->connect();
		if (is_null($length))
			return fgets($this->socket);
		else
			return fgets($this->socket, $length);
	}

	/**
	 * Writes the contents of string to the socket
	 *
	 * The number of bytes written is stored in {@link $written}.
	 *
	 * @param string $string The string that is to be written.
	 * @param int $length If the length argument is given, writing will stop after length bytes have been written or the end of string is reached, whichever comes first.
	 * @return Socket
	 */
	public function write($string, $length = null)
	{
		$this->connect();
		if (is_null($length))
			$this->written = fwrite($this->socket, $string);
		else
			$this->written = fwrite($this->socket, $string, $length);
		return $this;
	}

	/**
	 * Tests for end-of-file on this socket
	 *
	 * @return bool Returns TRUE if this socket is at EOF or an error occurs (including socket timeout); otherwise returns FALSE.
	 */
	public function eof()
	{
		return feof($this->socket);
	}

	/**
	 * Closes the socket
	 *
	 * If you have finished with the socket then consider destroying the socket instead
	 *
	 * <code>
	 *     // Destroy the socket
	 *     $socket = null;
	 * </code>
	 *
	 * @return Socket
	 */
	public function close()
	{
		if ( ! is_null($this->socket))
		{
			if (fclose($this->socket))
			{
				$this->socket = null;
			}
		}
		return $this;
	}

	/**
	 * Closes the socket when the socket is destroyed
	 * @internal
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * Prepare to be serialised
	 *
	 * @return array
	 * @internal
	 */
	public function __sleep()
	{
		return array('hostname', 'port', 'timeout');
	}

	/**
	 * Resume after serialisation
	 *
	 * Reconnects to the socket after being serialized
	 *
	 * @internal
	 */
	public function __wakeup()
	{
		$this->connect();
	}

}
