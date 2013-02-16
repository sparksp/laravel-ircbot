<?php

/**
 * Setup IRC (Migration)
 * 
 * <code>
 *  php artisan migrate
 * </code>
 * 
 * @package  IRC
 * @category  Bundle
 * @author  Phill Sparks <me@phills.me.uk>
 * @copyright  2012 Phill Sparks
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */
class IRC_Setup {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		mkdir(path('storage').'logs/irc', 0777);
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		rmdir(path('storage').'logs/irc');
	}

}