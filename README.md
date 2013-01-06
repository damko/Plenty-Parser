# Plenty Parser spark package for Codeigniter

## Note
This is the same code of this project https://github.com/Vheissu/Plenty-Parser but in a spark format

## Intro
Plenty Parser is a driver based parser library for Codeigniter 2.1 and up. It allows you to render view templates using different drivers. Out of the box Plenty Parser supports rendering using Smarty 3 and Twig templating libraries, because it doesn't override any in-built parsing or view capabilities of Codeigniter, you have yourself a swiss army knife of view loading.

## How to install
Currently this spark is not yet available on getsparks.org repositories so the best option is to do it manually
> * create the directory sparks/plenty_parser/0.0.1
> * clone or copy the content of this github repo in that directory
> * create these directories in application/ and make them writable:
> * * cache/smarty/cached
> * * cache/smarty/compiled
> * * cache/twig

When this spark will be on getsparks.org then you can use this command

	php tools/spark install -v0.0.1 pp

Don't forget to add the cache folder and to set it writable

## Configuration
After the installation edit the file plentyparse.php in the spark config directory

## How to load the driver

	$this->load->spark('pp/0.0.1');

after that you can use the Plenty Parser object in the usual way

	$this->pp->...
