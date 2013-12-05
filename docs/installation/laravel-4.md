## Install & Configure in Laravel 4

> **Note:** To use Cartalyst's Composite Config package you need to have a valid Cartalyst.com subscription.
Click [here](https://www.cartalyst.com/pricing) to obtain your subscription.

### 1. Composer {#composer}

----

Open your `composer.json` file and add the following lines:

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
	}

Run composer update from the command line

	composer update

### 2. Service Provider {#service-provider}

----

Add the following to the list of service providers in `app/config/app.php`.

	'Cartalyst\CompositeConfig\CompositeConfigServiceProvider',

### 3. Migrations {#migrations}

----

In order to run the migration successfully, you need to have a default database connection setup on your Laravel 4 application, once you have that setup, you can run the following command:

	php artisan migrate --package=cartalyst/composite-config

### 4. Configuration {#configuration}

----

After installing, you can publish the package's configuration file into your application, by running the following command:

	php artisan config:publish cartalyst/composite-config

This will publish the config file to `app/config/packages/cartalyst/composite-config/config.php` where you can modify the package configuration.
