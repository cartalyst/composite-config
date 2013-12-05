## Installing with Composer

> **Note:** To use Cartalyst's Composite Config package you need to have a valid Cartalyst.com subscription.
Click [here](https://www.cartalyst.com/pricing) to obtain your subscription.

### 1. Composer {#composer}

----

Open your `composer.json` file and add the following lines

	{
		"repositories": [
			{
				"type": "composer",
				"url": "http://packages.cartalyst.com"
			}
		],
		"require": {
			"cartalyst/composite-config": "1.0.*",
		},
		"minimum-stability": "stable"
	}

Run a composer update from the command line

	composer update

If you haven't yet, make sure to require Composer's autoload file in your app root to autoload the installed packages.

	require 'vendor/autoload.php';
