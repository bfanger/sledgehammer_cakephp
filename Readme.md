
# SledgeHammer CakePHP #

The CakePHP Framework, packaged as a SledgeHammer module.

run `php utils/bake_project.php` to setup all the cakephp default config.

For usage of the MVC stack place this code in your rewrite.php

```php
$Dispatcher = new \Dispatcher();
$Dispatcher->dispatch(new \CakeRequest(), new \CakeResponse(array('charset' => \Configure::read('App.encoding'))));
```