<?php
return array(

	'laravel-forum' => array(
		'message' => 'Forum Activity: (:title) (:link)',
		'url' => 'http://forums.laravel.com/extern.php?action=feed&type=rss',
		'channel' => '#laravel',
	),

	'laravel-github' => array(
		'message' => '(:title) (:link)',
		'url' => 'https://github.com/LaravelBot.private.atom?token=a6798a59cc4ebefb3415dc938a6ec1c2',
		'channel' => '#laravel',
	),

	// This feed doesn't validate :(
	// 'laravel-bundles' => array(
	// 	'message' => 'New Bundle: (:title) (:link)',
	// 	'url' => 'http://bundles.laravel.com/rss',
	// 	'channel' => '#laravel',
	// ),

);
