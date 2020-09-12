# Changelog

### v5.0.0 - 2020-09-12

- Updated for Laravel 8.

### v4.0.0 - 2020-03-03

- Updated for Laravel 7.

### v3.0.3 - 2019-09-23

`FIXED`

- A cached config bug.

### v3.0.2 - 2019-09-09

`FIXED`

- A config merging bug.

### v3.0.1 - 2019-09-08

`FIXED`

- Setting cached configs using dotted notation.

### v3.0.0 - 2019-08-31

- BC Break: PHP 7.2 is the minimum required PHP version
- BC Break: Laravel 6.0 is the minimum supported Laravel version

### v2.0.5 - 2018-04-19

`FIXED`

- Issue where calling Config::set at runtime was not overriding values that were stored on the database.

### v2.0.4 - 2015-12-24

`UPDATED`

- `composer.json` to fix Composer warnings about migrations.

### v2.0.3 - 2015-05-22

`FIXED`

- A class `cache` does not exist exception.

### v2.0.2 - 2015-03-16

`FIXED`

- Fetch and cache configurations on `register` so that they are available earlier in the lifecycle.

### v2.0.1 - 2015-03-02

`FIXED`

- Flush cache after persisting configs so that they're available on the same request.
- Service Provider attempting to access the cache manager before being initialized.

### v2.0.0 - 2015-02-24

- Refactored for Laravel 5.

### v1.1.1 - 2014-09-29

`REVISED`

- Removed table check to save a database query.

### v1.1.0 - 2014-08-18

`ADDED`

- Caching support.

### v1.0.3 - 2013-11-27

`REVISED`

- Loosen requirements to allow usage on Laravel 4.1.

### v1.0.2 - 2013-07-04

`ADDED`

- Added table config option.

### v1.0.1 - 2013-06-27

`REVISED`

- Take environment into consideration.

### v1.0.0 - 2013-05-28

`ADDED`

- Store configuration on the database.
- Retrieve configurations from the database.
- Automatically caches configurations.
- Automatically flushes cache when a new config is set.
