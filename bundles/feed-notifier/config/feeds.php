<?php
return array(

	'laravel-forum' => array(
		'message' => 'Forum Activity: (:title) (:link)',
		'url' => 'http://forums.laravel.com/extern.php?action=feed&type=rss',
		'channel' => '#laravel',
	),

	'laravel-google' => array(
		'message' => 'Google Alert: (:title) (:link)',
		'url' => 'http://www.google.com/alerts/feeds/15062750671952302504/1492468193374197150',
		'channel' => '#laravel',
		'clean_link' => function($url)
		{
			$parts = parse_url($url);
			$query = parse_str($parts['query']);

			if (isset($query['q'])) return $query['q'];

			return $url;
		},
	),

	'laravel-stackoverflow' => array(
		'message' => 'Stack Overflow: (:title) (:link)',
		'url' => 'http://stackoverflow.com/feeds/tag/laravel',
		'channel' => '#laravel',
	),

	'laravel-twitter' => array(
		'message' => 'New tweet by (:title) (:link)',
		'url' => 'http://search.twitter.com/search.atom?q=laravel%20laravelphp%20-RT%20-%40laravel&show_user=true&rpp=3',
		'channel' => '#laravel',
	),

	// This feed doesn't validate :(
	// 'laravel-bundles' => array(
	// 	'message' => 'New Bundle: (:title) (:link)',
	// 	'url' => 'http://bundles.laravel.com/rss',
	// 	'channel' => '#laravel',
	// ),

);
