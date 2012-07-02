

## Installation

Add the "sledgehammer" folder at the same level as the "cake" folder:

```
 Project folder
 |- app
 |- cake
 |- sledgehammer
 |  |- core
 |  |- cakephp
 |  |- ...
 |- tmp
 |- ...
```

Modify your app/config/bootstrap.php to include:

```
require (ROOT.'/sledgehammer/core/bootstrap.php');
```

## Goodies
Set database driver to "sledgehammer"