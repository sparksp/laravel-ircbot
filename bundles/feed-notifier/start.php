<?php

/**
 * Checks feeds periodically for new items and posts a notice to #laravel.
 *
 * @package  Feed-Notifier
 * @category  Bundle
 * @author  Shawn McCool
 * @author  Phill Sparks <me@phills.me.uk>
 * @license  MIT License <http://www.opensource.org/licenses/mit>
 */

use IRC\Message, IRC\Command;

// feed reader to abstract out attribute names

include __DIR__.'/vendor/simplepie.php';


function update_feeds()
{
	// Response store

	$response = array();

	// Get the config

	$ttl   = Config::get('feed-notifier::options.ttl');
	$feeds = Config::get('feed-notifier::feeds');

	if ($feeds) foreach ($feeds as $feed => $config)
	{
		$key = 'feed-updated '. $feed;

		if (!Cache::has($key))
		{
			// Ignore the first run, we don't want to have all new items returned
			// when there's no cache

			Cache::put($key, mktime(), $ttl);
		}
		else
		{
			// Message store

			$messages = array();

			// Last run timestamp
			
			$last = Cache::get($key);
			$next = $last;

			// Get the tokens from the message

			$token_count = preg_match_all('/\(:(\w+)\)/', $config['message'], $tokens);

			// Load feed data

			$rss = new SimplePie();
			$rss->set_feed_url($config['url']);
			$rss->enable_cache(false);
			$rss->init();

			$items = $rss->get_items();

			// Iterate items

			if ($items) foreach ($items as $item)
			{

				// Compare with last timestamp.  If it's newer than last timestamp
				// then let's process it

				$time = $item->get_date('U');
				$next = max($next, $time);

				if ($time > $last)
				{
					$message = $config['message'];

					if ($token_count)
					{
						// search and replace the tokens with values from the item
						$s = array(); $r = array();
						for ($i = 0, $il = count($tokens[0]); $i < $il; $i++)
						{
							$s[] = $tokens[0][$i];
							$method = 'get_'.$tokens[1][$i];
							$r[] = method_exists($item, $method) ? $item->$method() : '';
						}
						$message = str_replace($s, $r, $message);
					}

					$messages[] = html_entity_decode($message);
				}

				if (count($messages) >= 3) break; // Flood protection
			}

			Cache::put($key, $next, $ttl);

			// Build the response messages

			foreach($messages as $message)
			{
				$response[] = Message::notice($config['channel'], $message);
			}
		}

		// Return the response, do this in the for loop to prevent flooding.  Hopefully
		// only one feed per ping will be handled.

		if (count($response))
		{
			return $response;
		}
	}

	// Return the responses

	if (count($response))
	{		
		return $response;
	}
}

// Use the server PING message to check for new bundles.  We use a 10 minute
// cache so that we don't do the HTTP request on every ping.
Message::listen('ping', function($message)
{
	return update_feeds();
});
