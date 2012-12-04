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

			$doc = new DomDocument();
			@$doc->loadHTML($r1->data);

			//get an array of all the li.g items
			$finder = new DomXPath($doc);
			$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' g ')]");
			$resultHref = false;

			//traverse the dom for each returned node until a relevant result is found
			if ($nodes)
			{
				foreach ($nodes as $node)
				{
					if ($node->childNodes)
					{
						$anchor = $node->firstChild->firstChild;
						$hrefParts = explode('&rct', substr($anchor->getAttribute('href'), 9));
						$href = $hrefParts[0];

						if (strpos($href, 'http://laravel.com/docs') !== false)
						{
							$resultHref = $href;
							break;
						}
					}
				}
			}

			//if a link was found, post it back to #laravel
			if ($resultHref)
			{
				$channel = $message->channel() ?: '#laravel';
				return Message::privmsg($channel, "Did someone ask for some docs? Here you go: " . $resultHref);
			}


		}
	}
};
Message::listen('privmsg', $observer);