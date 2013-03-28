<?php

/*
|--------------------------------------------------------------------------
| Auto-Loader Mappings
|--------------------------------------------------------------------------
|
| Laravel uses a simple array of class to path mappings to drive the class
| auto-loader. This simple approach helps avoid the performance problems
| of searching through directories by convention.
|
| Registering a mapping couldn't be easier. Just pass an array of class
| to path maps into the "map" function of Autoloader. Then, when you
| want to use that class, just use it. It's simple!
|
*/

Autoloader::map(array(
	'Base_Controller' => path('app').'controllers/base.php',
));

/*
|--------------------------------------------------------------------------
| Auto-Loader Directories
|--------------------------------------------------------------------------
|
| The Laravel auto-loader can search directories for files using the PSR-0
| naming convention. This convention basically organizes classes by using
| the class namespace to indicate the directory structure.
|
| So you don't have to manually map all of your models, we've added the
| models and libraries directories for you. So, you can model away and
| the auto-loader will take care of the rest.
|
*/

Autoloader::directories(array(
	path('app').'models',
	path('app').'libraries',
));
