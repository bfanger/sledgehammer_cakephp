<?php
/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace SledgeHammer;

if (!defined('CAKE_CORE_INCLUDE_PATH')) {
	warning('CakePHP not loaded', 'Add "require (dirname(__FILE__).\'/../../sledgehammer/core/init_framework.php\'); to app/config/bootstrap.php"');
	return;
}
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
error_reporting(E_ALL & ~E_DEPRECATED); // Ignore the E_STRICT & E_DEPRECATED error messages
?>
