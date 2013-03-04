<?php

/*
|--------------------------------------------------------------------------
| Bundle Configuration
|--------------------------------------------------------------------------
|
| Bundles allow you to conveniently extend and organize your application.
| Think of bundles as self-contained applications. They can have routes,
| controllers, models, views, configuration, etc. You can even create
| your own bundles to share with the Laravel community.
|
| This is a list of the bundles installed for your application and tells
| Laravel the location of the bundle's root directory, as well as the
| root URI the bundle responds to.
|
| For example, if you have an "admin" bundle located in "bundles/admin"
| that you want to handle requests with URIs that begin with "admin",
| simply add it to the array like this:
|
|		'admin' => array(
|			'location' => 'admin',
|			'handles'  => 'admin',
|		),
|
| Note that the "location" is relative to the "bundles" directory.
| Now the bundle will be recognized by Laravel and will be able
| to respond to requests beginning with "admin"!
|
| Have a bundle that lives in the root of the bundle directory
| and doesn't respond to any requests? Just add the bundle
| name to the array and we'll take care of the rest.
|
*/

return array(
	'irc' => array(
		'autoloads' => array(
			'map' => array(
				'IRC\\Client' => '(:bundle)/lib/client.php',
				'IRC\\Command' => '(:bundle)/lib/command.php',
				'IRC\\Exception' => '(:bundle)/lib/exception.php',
				'IRC\\Message' => '(:bundle)/lib/message.php',
				'IRC\\Sender' => '(:bundle)/lib/sender.php',
			),
		),
	),
	'eloquent' => array(
	    'autoloads' => array(
	        'map' => array(
	            'Eloquent\\Model'    => '(:bundle)/model.php',
	            'Eloquent\\Hydrator' => '(:bundle)/hydrator.php',
	        ),
	    ),
	),
	'irc-greeter' => array('auto' => true),
	'feed-notifier' => array('auto' => true),
	'irc-docsearch' => array('auto' => true),
    'help' => array('auto' => true),
);