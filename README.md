# Overview

## Streunerkatzen
### Suche: 
* reminder email nach x wochen
* login / bearbeitung + absegnung
* farben kategorisierung - beispiele
* zeitraum

### Review:
* mitglied werden: adresse nur optional (falls xx checkbox angehakt wird)

### Dev Environment:
* put this repo inside htdocs/streunerkatzen-silverstripe (directory name is important)
* install composer
* run `composer install`
* import streunerkatzen.sql

### Troubleshooting
* If root only lists directory, make sure mod_rewrite is enabled
* If object not found error appears on root, make sure dir is htdocs/streunerkatzen-silverstripe
* If login doesn't work (streunerkatzen_silverstripe@gernotraudner.at), create new db "streunerkatzen" and import streunerkatzen.sql. Make sure it doesn't contain any tables before importing.
* If backend resources are missing, make sure dir is correct. Call `composer vendor-expose` to refresh symlinks.

## Silverstripe
Base project folder for a SilverStripe ([http://silverstripe.org](http://silverstripe.org)) installation. Required modules are installed via [http://github.com/silverstripe/recipe-cms](http://github.com/silverstripe/recipe-cms). For information on how to change the dependencies in a recipe, please have a look at [https://github.com/silverstripe/recipe-plugin](https://github.com/silverstripe/recipe-plugin). In addition, installer includes [theme/simple](https://github.com/silverstripe-themes/silverstripe-simple) as a default theme.

### Links:
 * [Changelogs](http://doc.silverstripe.org/framework/en/changelogs/)
 * [Bugtracker: Framework](https://github.com/silverstripe/silverstripe-framework/issues)
 * [Bugtracker: CMS](https://github.com/silverstripe/silverstripe-cms/issues)
 * [Bugtracker: Installer](https://github.com/silverstripe/silverstripe-installer/issues)
 * [Forums](http://silverstripe.org/forums)
 * [Developer Mailinglist](https://groups.google.com/forum/#!forum/silverstripe-dev)
 * [License](./LICENSE)
