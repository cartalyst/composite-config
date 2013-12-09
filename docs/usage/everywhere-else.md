## Everywhere Else

&nbsp;

### Setup the Composite Config loader {#setup-loader}

---

	$loader = new Cartalyst\CompositeConfig\CompositeLoader(new Illuminate\Filesystem\Filesystem, '/path/to/config/files');

	// Attach the optional database loading functionality. Without this, the composite loader acts like the default file loader.
	$database = new Illuminate\Database\Connection(new PDO('mysql:dbname=my_database;host=127.0.0.1'), $prefix = '');
	$loader->setDatabase($database);
	$config = new Illuminate\Config\Repository($loader);

### Retrieving Config {#retrieve-config}

---

	$config->get($key);

### Saving Config {#save-config}

---

There are two ways of saving configuration items.

#### 1. Runtime

To set configuration at runtime, use

	$config->set($key, $value);

During that request, calling `$config->get($key);` will return the value you have set.

> **Note:** Configuration values that are set at run-time are only set for the current request, and will not be carried over to subsequent requests.

#### 2. Persisting

To set persisting configuration at runtime, use

	$config->getLoader()->set($key, $value);

> **Note:** When persisting a config item, the value will be (by default) persisted for the current environment only.
Ex. if you're running in the 'local' environment and switch to 'production', your item won't load.
Overcoming this is easy, just provide '*' as the third parameter - `$config->getLoader()->set($key, $value, '*');` and it will work for all environments.


##### Cascading

---

Below is the order in which items are cascaded:

1. Database configuration for the current environment
2. Database configuration for all environments (persisted by providing '*' as the third parameter)
3. Filesystem configuration for the current environment
4. Filesystem configuration for all environmentts

Any number of these may be absent, it will be skipped.
