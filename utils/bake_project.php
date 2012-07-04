<?php
namespace Sledgehammer;
define('BAKING', true);
include(dirname(__FILE__).'/../../core/bootstrap.php');

echo "Copying required CakePHP files to the application folder.\n";
$tpldir = dirname(__FILE__).'/../Cake/Console/Templates/skel/';
mkdirs(APPLICATION_DIR.'Config');
copy($tpldir.'Config/core.php', APPLICATION_DIR.'Config/core.php');
copy($tpldir.'Config/bootstrap.php', APPLICATION_DIR.'Config/bootstrap.php');
copy($tpldir.'Config/routes.php', APPLICATION_DIR.'Config/routes.php');
copy($tpldir.'Config/database.php.default', APPLICATION_DIR.'Config/database.php');

// Replace default cipher and salt for (improved) Security
$contents = file_get_contents(APPLICATION_DIR.'Config/core.php');
$cipher = '';
for ($i = 0; $i < 30; $i++) {
	$cipher .= rand(0,9);
}
$contents = str_replace('76859309657453542496749683645', $cipher, $contents);
$contents = str_replace('DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi', sha1(microtime(true).'cakephp'.$cipher), $contents);
file_put_contents(APPLICATION_DIR.'Config/core.php', $contents);

// Add default css to the application
copydir($tpldir.'webroot/css', APPLICATION_DIR.'public/css');
// Add images to the application
copydir($tpldir.'webroot/img', APPLICATION_DIR.'public/img');

// Copy MVC superclasses
copy($tpldir.'Model/AppModel.php', APPLICATION_DIR.'classes/AppModel.php');
copy($tpldir.'Controller/AppController.php', APPLICATION_DIR.'classes/AppController.php');
copy($tpldir.'View/Helper/AppHelper.php', APPLICATION_DIR.'classes/AppHelper.php');

//Copy Pages
copy($tpldir.'Controller/PagesController.php', APPLICATION_DIR.'classes/PagesController.php');
echo "  Done.\n"
?>
