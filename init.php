<?php
namespace SledgeHammer;
/**
 * @package CakePHP
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', substr(PATH, 0, -1));
define('WWW_ROOT', PATH.'public/');
define('APP_DIR', basename(APPLICATION_DIR));
define('TMP',  TMP_DIR.	'cakephp/');

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