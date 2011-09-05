<?php
/**
 * SledgeHammer adjustments for CakePHP 
 * 
 * @package CakePHP
 */
namespace SledgeHammer;

if (!defined('CAKE_CORE_INCLUDE_PATH')) { // Is CakePHP loaded?
	warning('CakePHP not loaded', 'Add "require (ROOT.\'/sledgehammer/core/init_framework.php\'); to app/config/bootstrap.php"');
	return;
}

$GLOBALS['AutoLoader']->standalone = false; //

// AutoLoading of CakePHP classes is deactivated, use App::import(), var $uses, etc
// Use the SledgeHammer library inside a CakePHP project
/*
$cakePath = ROOT.'/cake/';
$GLOBALS['AutoLoader']->importModule(array(
	'name' => 'CakePHP',
	'path' => $cakePath,
), array(
	'ignore_folders' => array($cakePath.'console', $cakePath.'tests'),
	'ignore_files' => array($cakePath.'libs/overloadable_php4.php')
));
$GLOBALS['AutoLoader']->importModule(array(
	'name' => 'CakeApplication',
	'path' => ROOT.'/app/',
), array(
	'ignore_folders' => array(ROOT.'/app/tests'),
));
*/
// Ignore the E_STRICT & E_DEPRECATED error messages 
error_reporting(E_ALL & ~E_DEPRECATED); 

?>
