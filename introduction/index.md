## Introduction

Cartalyst's Composite Config package enhances `illuminate/config` to allow configuration items to be placed within a database whilst cascading back to the filesystem.

This is super useful for building user interfaces that facilitate editing configuration for an app. Because it does not change the API for retrieving configuration items, it degrades gracefully to the filesystem if not present and requires zero changes to the places which use the configuration items.

The package requires PHP 8.0+ and follows the FIG standard PSR-4 to ensure a high level of interoperability between shared PHP code and is fully unit-tested.

### Getting started

Have a [read through the Installation Guide](#installation).

### Quick Example

	// Set config at runtime
	Config::set($key, $value);

	// Set persisting config at runtime
	Config::persist($key, $value);
