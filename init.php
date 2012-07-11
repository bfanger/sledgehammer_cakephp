<?php
namespace Sledgehammer;
/**
 * @package CakePHP
 */
if (is_dir(APPLICATION_DIR.'Config') == false) {
	if (defined('BAKING')) {
		return;
	}
	notice('Missing "APP/Config/" folder, run `php cakephp/utils/bake_project.php`');

}
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', substr(PATH, 0, -1));
define('WWW_ROOT', PATH.'public/');
define('APP_DIR', basename(APPLICATION_DIR));
define('TMP',  TMP_DIR.	'cakephp/');

if (ENVIRONMENT === 'phpunit') {
	define('CORE_TEST_CASES', dirname(__FILE__).'/Cake/Test/Case');
	Framework::$autoLoader->importFolder(dirname(__FILE__).'/Cake/TestSuite/', array('mandatory_superclass' => false));
}
mkdirs(TMP.'cache/persistent');
mkdirs(TMP.'cache/models');
mkdirs(TMP.'cache/views');
mkdirs(TMP.'sessions');
mkdirs(TMP.'logs');
mkdirs(TMP.'tests');

$useAutoloader = Framework::$autoLoader->standalone;
Framework::$autoLoader->standalone = false; // temporarely disable the AutoLoader

// Initialize CakePHP
include(dirname(__FILE__).'/Cake/bootstrap.php');

Framework::$autoLoader->standalone = $useAutoloader;

?>