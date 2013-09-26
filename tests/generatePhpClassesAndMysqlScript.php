<?php

// Initialize translation tool
require_once(__DIR__.'/../lib/translation.php');
Translation::init(
	new TranslatedSentencesProvider_File(__DIR__.'/../data/translations.txt'),
	null,
	new LanguageProvider_Variable('en')
);

/**
 * Generate PHP classes and MySQL script
 * @param $definitionsFilename string definitions filename
 * @param $mysqlScriptFilename string MySQL script filename
 * @param $phpClassesFilename string PHP clases filename
 */
function generatePhpClassesAndMysqlScript($definitionsFilename,$mysqlScriptFilename,$phpClassesFilename)
{
	// Generate PHP classes and MySQL script
	require_once(__DIR__.'/../lib/wph/require.php');
	$definitions = file_get_contents($definitionsFilename);
	$project = Loader::load($definitions)->compile();
	$mysqlGenerator = new MySQL_Generator($project);
	$phpGenerator = new PHP_Generator($project,$mysqlGenerator);
	if(file_exists($mysqlScriptFilename)) {
		unlink($mysqlScriptFilename);
	}
	$mysqlGenerator->generate_file($mysqlScriptFilename);
	if(file_exists($phpClassesFilename)) {
		unlink($phpClassesFilename);
	}
	$phpGenerator->generate_file($phpClassesFilename);
}