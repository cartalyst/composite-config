## Usage

### Retrieving Config

Usage is identical to [that explained in the Laravel documentation](http://laravel.com/docs/configuration#introduction)

	Config::get($key);

### Saving Config

There are two ways of saving configuration items.

#### 1. Runtime

To set configuration at runtime, use

	Config::set($key, $value);

During that request, calling `Config::get($key);` will return the value you have set.

> **Note:** Configuration values that are set at run-time are only set for the current request, and will not be carried over to subsequent requests.

#### 2. Persisting

To set persisting configuration at runtime, use

	Config::persist($key, $value);
