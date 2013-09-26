<?php

/**
 * PHP tool to apply MVC pattern
 * @author Cyril Perrin
 * @license LGPL v3
 * @version 2013-10-08
 */

// Require configuration
require('configuration.php');

// Require application
require('application.php');

// Require route and request
require('route.php');
require('request.php');

// Require controller
require('controller.php');

// Require renders
require('view.php');
require('layout.php');
require('partial.php');

// Require tools
require('filter.php');
require('service.php');