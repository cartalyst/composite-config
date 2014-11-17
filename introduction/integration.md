## Integration

### Laravel 4

The Composite Config package has optional support for Laravel 4 and it comes bundled with a
Service Provider for easier integration.

After you have installed the package correctly, just follow the instructions.

Open your Laravel config file `app/config/app.php` and add the following lines.

In the `$providers` array add the following service provider for this package.

	'Cartalyst\CompositeConfig\CompositeConfigServiceProvider',

### Migrations

In order to run the migration successfully, you need to have a default database connection setup on your Laravel 4 application, once you have that setup, you can run the following command:

	php artisan migrate --package=cartalyst/composite-config

### Configuration

After installing, you can publish the package's configuration file into your
application by running the following command:

	php artisan config:publish cartalyst/composite-config

This will publish the config file to `app/config/packages/cartalyst/composite-config/config.php`
where you can modify the package configuration.
