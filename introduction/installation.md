## Installation

The best way to install the Composite Config package is quickly and easily done with [Composer](http://getcomposer.org).

Open your `composer.json` and add the following to the `require` array

	"cartalyst/composite-config": "1.0.*"

Add the following lines after the `require` array on your `composer.json` file

	"repositories": [
		{
			"type": "composer",
			"url": "https://packages.cartalyst.com"
		}
	]

> **Note:** Make sure your `composer.json` file is in a valid JSON format after the required changes.

### Install the dependencies

Run Composer to install or update the new requirement.

	php composer install

or

	php composer update

Now you are able to require the `vendor/autoload.php` file to PSR-0 autoload the library.

### Example

	// Include the composer autoload file
	require_once 'vendor/autoload.php';

	// Import the necessary classes
	use Cartalyst\CompositeConfig\CompositeLoader;
	use Illuminate\Config\Repository;
	use Illuminate\Database\Connection;
	use Illuminate\Filesystem\Filesystem;

	// Setup config loader
	$loader = new CompositeLoader(new Filesystem(), '/path/to/config/files');

	// Attach the optional database loading functionality.
	// Without this, the composite loader acts like the default file loader.
	$database = new Connection(new PDO('mysql:dbname=my_database;host=127.0.0.1'), $prefix = '');
	$loader->setDatabase($database);

	// Instantiate config
	$config = new Repository($loader);

	// Set persisting config
	$config->getLoader()->set($key, $value);
