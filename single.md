<h2>Introduction</h2>

<p>Cartalyst's Composite Config package enhances <code>illuminate/config</code> to allow configuration items to be placed within a database whilst cascading back to the filesystem.</p>

<p>This is super useful for building user interfaces that facilitate editing configuration for an app. Because it does not change the API for retrieving configuration items, it degrades gracefully to the filesystem if not present and requires zero changes to the places which use the configuration items.</p>

<p>The package follows the FIG standard PSR-0 to ensure a high level of<br>
interoperability between shared PHP code and is fully unit-tested.</p>

<h3>Getting started</h3>

<p>The package requires at least PHP version 5.3.</p>

<p>Have a <a href="#installation">read through the Installation Guide</a>.</p>

<h4>Quick Example</h4>

<pre><code>// Set config at runtime
Config::set($key, $value);

// Set persisting config at runtime
Config::getLoader()-&gt;set($key, $value);
</code></pre><h2>Installation</h2>

<p>The best way to install the Composite Config package is quickly and easily done with <a href="http://getcomposer.org">Composer</a>.</p>

<p>Open your <code>composer.json</code> and add the following to the <code>require</code> array</p>

<pre><code>"cartalyst/composite-config": "1.0.*"
</code></pre>

<p>Add the following lines after the <code>require</code> array on your <code>composer.json</code> file</p>

<pre><code>"repositories": [
    {
        "type": "composer",
        "url": "https://packages.cartalyst.com"
    }
]
</code></pre>

<blockquote>
<p><strong>Note:</strong> Make sure your <code>composer.json</code> file is in a valid JSON format after the required changes.</p>
</blockquote>

<h3>Install the dependencies</h3>

<p>Run Composer to install or update the new requirement.</p>

<pre><code>php composer install
</code></pre>

<p>or</p>

<pre><code>php composer update
</code></pre>

<p>Now you are able to require the <code>vendor/autoload.php</code> file to PSR-0 autoload the library.</p>

<h3>Example</h3>

<pre><code>// Include the composer autoload file
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
$loader-&gt;setDatabase($database);

// Instantiate config
$config = new Repository($loader);

// Set persisting config
$config-&gt;getLoader()-&gt;set($key, $value);
</code></pre><h2>Integration</h2>

<h3>Laravel 4</h3>

<p>The Composite Config package has optional support for Laravel 4 and it comes bundled with a<br>
Service Provider for easier integration.</p>

<p>After you have installed the package correctly, just follow the instructions.</p>

<p>Open your Laravel config file <code>app/config/app.php</code> and add the following lines.</p>

<p>In the <code>$providers</code> array add the following service provider for this package.</p>

<pre><code>'Cartalyst\CompositeConfig\CompositeConfigServiceProvider',
</code></pre>

<h3>Migrations</h3>

<p>In order to run the migration successfully, you need to have a default database connection setup on your Laravel 4 application, once you have that setup, you can run the following command:</p>

<pre><code>php artisan migrate --package=cartalyst/composite-config
</code></pre>

<h3>Configuration</h3>

<p>After installing, you can publish the package's configuration file into your<br>
application by running the following command:</p>

<pre><code>php artisan config:publish cartalyst/composite-config
</code></pre>

<p>This will publish the config file to <code>app/config/packages/cartalyst/composite-config/config.php</code><br>
where you can modify the package configuration.</p><h2>Usage</h2>

<h3>Retrieving Config</h3>

<p>Usage is identical to <a href="http://laravel.com/docs/configuration#introduction">that explained in the Laravel documentation</a></p>

<pre><code>Config::get($key);
</code></pre>

<h3>Saving Config</h3>

<p>There are two ways of saving configuration items.</p>

<h4>1. Runtime</h4>

<p>To set configuration at runtime, use</p>

<pre><code>Config::set($key, $value);
</code></pre>

<p>During that request, calling <code>Config::get($key);</code> will return the value you have set.</p>

<blockquote>
<p><strong>Note:</strong> Configuration values that are set at run-time are only set for the current request, and will not be carried over to subsequent requests.</p>
</blockquote>

<h4>2. Persisting</h4>

<p>To set persisting configuration at runtime, use</p>

<pre><code>Config::getLoader()-&gt;set($key, $value);
</code></pre>

<blockquote>
<p><strong>Note:</strong> When persisting a config item, the value will be (by default) persisted for the current environment only.<br>
Ex. if you're running in the 'local' environment and switch to 'production', your item won't load.<br>
Overcoming this is easy, just provide '*' as the third parameter - <code>Config::getLoader()-&gt;set($key, $value, '*');</code> and it will work for all environments.</p>
</blockquote>

<h3>Cascading</h3>

<p>Below is the order in which items are cascaded:</p>

<ol>
<li>Database configuration for the current environment</li>
<li>Database configuration for all environments (persisted by providing '*' as the third parameter)</li>
<li>Filesystem configuration for the current environment</li>
<li>Filesystem configuration for all environmentts</li>
</ol>

<p>Any number of these may be absent, it will be skipped.</p>

<h3>Limitations</h3>

<p>In Laravel 4, configuration is used to resolve database credentials as well as a number of core options. Because of this, any config items requested before the composite config package is loaded will be cached. Typically, this is just the config within <code>app/config/app.php</code> and <code>app/config/database.php</code> and <code>app/config/session.php</code>. There is a way around this if you require to override these config items:</p>

<pre><code>Config::set('*::app', null);
Config::set('*::database', null);
Config::set('*::session', null);
</code></pre>

<p>This will remove these items from the cache and force them to be re-fetched from the database. Be sure to inject the new values into anywhere they've been previously injected.</p>

<blockquote>
<p><strong>Note:</strong> Most people shouldn't need to worry about the above.</p>
</blockquote>