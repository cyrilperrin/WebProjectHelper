<?php

// Force using cookie to store session
ini_set('session.use_only_cookies', '1');

// Define release
define('RELEASE','1.7.4');

// Define root
define('ROOT_DIR', realpath(dirname(__FILE__).'/..').'/');

// Add directories to path
set_include_path(implode(PATH_SEPARATOR, array(ROOT_DIR.'lib',get_include_path())));

// Require libraries
require('mvc/require.php');
require('wph/require.php');
require('zip.php');
require('translation.php');

// Get abstract controller
require(ROOT_DIR.'app/controllers/abstract.php');

// Définir les routes
Request::addRoute(new Route(
	array(
		':controller/:action:parameters',
		array(
			':parameters' => array(
				'/:name/:value'
			)
		)
	),
	array(
		'(:controller(/:action(:parameters)?)?)?/?',
		array(
			':controller' => '[a-zA-Z][a-zA-Z0-9]*',
			':action' => '[a-zA-Z][a-zA-Z0-9]*',
			':parameters' => array(
				'/?:name(/:value)?', array(
					':name' => '[a-zA-Z][a-zA-Z0-9]*',
					':value' => '[^\/]*'
				)
			)
		)
	)
));

// Define default module path
Configuration::getInstance()->set(Configuration::PATH_MODULE,ROOT_DIR.'app/');

// Define default layout name
Configuration::getInstance()->set(Configuration::LAYOUT_DEFAULT,'main');

// Start session
session_start();

// Start services
Application::startService('log');
Application::startService('translation');

// Get configuration
$conf = Application::getService('conf');

// Define base URL
Request::setBase($conf['MISC']['BASE']);

// Start application
Application::run($conf['MISC']['DEV'] ? Application::MODE_DEVELOPMENT : Application::MODE_PRODUCTION);