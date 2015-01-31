<?php

// Generate PHP classes and MySQL script
require_once(__DIR__.'/../generatePhpClassesAndMysqlScript.php');
generatePhpClassesAndMysqlScript(
	__DIR__.'/definitions.txt',
	__DIR__.'/script.sql',
	__DIR__.'/classes.php'
);