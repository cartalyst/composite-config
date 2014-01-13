# Overview

### Retrieving Config {#retrieve-config}

Usage is identical to [that explained in the Laravel documentation](http://laravel.com/docs/configuration#introduction)

	Config::get($key);

### Saving Config {#save-config}

There are two ways of saving configuration items.

#### 1. Runtime

To set configuration at runtime, use

	Config::set($key, $value);

During that request, calling `Config::get($key);` will return the value you have set.

> **Note:** Configuration values that are set at run-time are only set for the current request, and will not be carried over to subsequent requests.

#### 2. Persisting

To set persisting configuration at runtime, use

	Config::getLoader()->set($key, $value);

> **Note:** When persisting a config item, the value will be (by default) persisted for the current environment only.
Ex. if you're running in the 'local' environment and switch to 'production', your item won't load.
Overcoming this is easy, just provide '*' as the third parameter - `Config::getLoader()->set($key, $value, '*');` and it will work for all environments.


### Cascading {#cascading}

Below is the order in which items are cascaded:

1. Database configuration for the current environment
2. Database configuration for all environments (persisted by providing '*' as the third parameter)
3. Filesystem configuration for the current environment
4. Filesystem configuration for all environmentts

Any number of these may be absent, it will be skipped.

### Limitations {#limitations}

In Laravel 4, configuration is used to resolve database credentials as well as a number of core options. Because of this, any config items requested before the composite config package is loaded will be cached. Typically, this is just the config within `app/config/app.php` and `app/config/database.php` and `app/config/session.php`. There is a way around this if you require to override these config items:


	Config::set('*::app', null);
	Config::set('*::database', null);
	Config::set('*::session', null);

This will remove these items from the cache and force them to be re-fetched from the database. Be sure to inject the new values into anywhere they've been previously injected.

> **Note:** Most people shouldn't need to worry about the above.
