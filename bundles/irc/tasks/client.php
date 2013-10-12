<?php

use Laravel\CLI\Tasks\Task;

/**
 * Core IRC Task
 * 
 * <code>
 *  php artisan irc::client
 * </code>
 * 
 * @package  IRC
 * @category  Bundle
 * @author  Phill Sparks <me@phills.me.uk>
 * @copyright  2012 Phill Sparks
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */
class IRC_Client_Task extends Task {

	/**
	 * @return void
	 */
	public function run()
	{
		set_time_limit(0);

		$irc = new IRC\Client;
	}

}
