## Integration

### Laravel

The Composite Config package has optional support for Laravel 9 and it comes bundled with a Service Provider for easier integration.

After you have installed the package correctly, just follow the instructions.

Open your Laravel config file `config/app.php` and add the following lines.

In the `$providers` array add the following service provider for this package.

	'Cartalyst\CompositeConfig\Laravel\CompositeConfigServiceProvider',

### Assets

Run the following commands to publish the migration and config files.

`php artisan vendor:publish --provider="Cartalyst\CompositeConfig\Laravel\CompositeConfigServiceProvider"`

#### Migrations

Run the following command to migrate Sentinel after publishing the assets.

`php artisan migrate`

#### Configuration

After publishing, the composite config config file can be found under `config/cartalyst.composite-config.php` where you can modify the package configuration.
