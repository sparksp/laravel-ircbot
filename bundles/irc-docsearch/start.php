<?php

/**
 * Responds to '!docs [search term]' with a link to the top google result in the laravel docs for that term
 *
 * @package  IRC-Docsearch
 * @category  Bundle
 * @author  Jan Hartigan <jan@frozennode.com>
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */

use IRC\Message;

include __DIR__.'/libraries/curl.php';
include __DIR__.'/libraries/simple_html_dom.php';


/**
 * Takes an href from a google search results page and determines whether it's good or bad
 *
 * @param string	$href
 *
 * @return string|false 	The string link if valid or false if invalid
 */
function processHref($href)
{
	//there is sometimes a strange prefix which must be removed
	if (strpos($href, 'http://laravel.com/docs') === 9)
	{
		$href = substr($href, 9);
	}

	//split the string on the ampersand and get the first part
	//so http://laravel.com/docs/eloquent&rct=j&q=.... would become http://laravel.com/docs/eloquent
	$hrefParts = explode('&', $href);
	$href = $hrefParts[0];

	//if it is a valid laravel docs page
	if (strpos($href, 'http://laravel.com/docs') !== false && $href !== 'http://laravel.com/docs')
	{
		return str_replace('%23', '#', $href);
	}
	else
	{
		return false;
	}
}

// Rommie will watch out for people saying '!docs [search term]'
$observer = function($message)
{
	$nick = strtolower($message->sender->nick);
	$body = end($message->params);
	$starter = "!docs ";

	if (starts_with($body, $starter))
	{
		//grab the search term
		$search = trim(substr($body, strlen($starter)));

		//if the search term is a valid string,
		if ($search !== false && $search !== '')
		{
			$session = new curl();
			$m1 = new curl_request();
			$m1->set_url("http://google.com/search?q=laravel.com%2Fdocs+" . urlencode($search));

			$r1 = $session->run($m1); // returns a curl_response object
			$html = str_get_html($r1->data);
			$resultHref = false;

			//first find the result list items and iterate over them
			if ($results = $html->find('#rso li.g'))
			{
				foreach ($results as $result)
				{
					//now check for the "Jump to" link which should be at "li.g span.st a" if it exists
					if ($jumpToLink = $result->find('span.st a'))
					{
						foreach ($jumpToLink as $link)
						{
							//if the href is valid, break the loop
							if ($resultHref = processHref($link->href))
							{
								break;
							}
						}

					}

					//if a link was found, break the loop
					if ($resultHref)
					{
						break;
					}

					//otherwise check the main link
					if ($mainLink = $result->find('h3.r a'))
					{
						foreach ($mainLink as $link)
						{
							//if the href is valid, break the loop
							if ($resultHref = processHref($link->href))
							{
								break;
							}
						}
					}

					//if a link was found, break the search
					if ($resultHref)
					{
						break;
					}
				}
			}

			//if a link was found, post it back to #laravel
			if ($resultHref)
			{
				$channel = $message->channel() ?: $message->sender->nick;
				return Message::privmsg($channel, "Did someone ask for some docs? Here you go: " . $resultHref);
			}
			// give some feedback if the search lead to no results
			else
			{
				$channel = $message->channel() ?: $message->sender->nick;
				return Message::privmsg($channel, "Bummer! I couldn't find that doc for you. Sorry, I really tried...");
			}


		}
	}
};
Message::listen('privmsg', $observer);