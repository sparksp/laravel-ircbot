<?php

/**
 * Checks periodically for new bundles and posts a notice to #laravel.
 * 
 * @package  IRC-Bundles
 * @category  Bundle
 * @author  Phill Sparks <me@phills.me.uk>
 * @copyright  2012 Phill Sparks
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */

use IRC\Message, IRC\Command;

$updated = Cache::get('bundle-rss', '');

// Use the server PING message to check for new bundles.  We use a 10 minute
// cache so that we don't do the HTTP request on every ping.
Message::listen('ping', function($message) use (&$updated)
{
	$response = array();

	if ( ! Cache::has('bundle-rss'))
	{
		$rss = file_get_contents("http://bundles.laravel.com/rss");
		// Unescaped &, Unknown entities, i.e., auml
		$rss = preg_replace('/&(?!amp;|quot;|lt;|gt;|\d{1,3};)/', '&amp;', $rss);

		try
		{
			$xml = new SimpleXMLElement($rss);
			$namespaces = $xml->getNamespaces(true);

			$items = array();

			foreach ($xml->channel->item as $item)
			{
				$dc = $item->children($namespaces['dc']);

				$bundle = array(
					'name' => (string)$item->title,
					'link' => (string)$item->link,
					'desc' => (string)$item->description,
					'date' => (string)$dc->date,
				);

				if (strcmp($updated, $bundle['date']) < 0)
				{
					$response[] = Message::privmsg('#laravel', 'New Bundle: '. $bundle['name'] .' '. $bundle['link']);

					// Remember the date and stop looking for more
					$updated = $bundle['date'];
					break;
				}
			}

			// Store the RSS for 10 minutes
			Cache::put('bundle-rss', $updated, 10);
		}
		catch (Exception $e)
		{
			// Ignore
		}
	}
	return $response;
});

// Forget the bundle cache
Command::register('bundle-reset', function($command)
{
	Cache::forget('bundle-rss');
});

// Give the user some help if they ask for it... need a better way of managing help
Command::register('help', function($command)
{
	return Message::privmsg($command->sender->nick,
		'IRC Bundles: Bundles are checked periodically on PING, use "bundle-reset" to clear the cache.');
});

