<?php
namespace SledgeHammer;
include(dirname(__FILE__).'/../../core/init.php');

copydir(dirname(__FILE__).'/../Cake/Console/Templates/skel/Config/', APPLICATION_DIR.'Config');

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
copydir(dirname(__FILE__).'/../Cake/Console/Templates/skel/webroot/css', APPLICATION_DIR.'public/css');
// Add images to the application
copydir(dirname(__FILE__).'/../Cake/Console/Templates/skel/webroot/img', APPLICATION_DIR.'public/img');

?>
