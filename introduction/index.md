## Introduction

Cartalyst's Composite Config package enhances `illuminate/config` to allow configuration items to be placed within a database whilst cascading back to the filesystem.

This is super useful for building user interfaces that facilitate editing configuration for an app. Because it does not change the API for retrieving configuration items, it degrades gracefully to the filesystem if not present and requires zero changes to the places which use the configuration items.

The package follows the FIG standard PSR-0 to ensure a high level of
interoperability between shared PHP code and is fully unit-tested.

### Getting started

The package requires at least PHP version 5.3.

Have a [read through the Installation Guide](#installation).

### Quick Example

	// Set config at runtime
	Config::set($key, $value);

	// Set persisting config at runtime
	Config::getLoader()->set($key, $value);
