#Composite Config

[![Build Status](http://ci.cartalyst.com/build-status/svg/10)](http://ci.cartalyst.com/build-status/view/10)

Our composite config package enhances `illuminate/config` to allow configuration items to be placed within a database whilst cascading back to the filesystem.

This is super useful for building user interfaces that facilitate editing configuration for an app. Because it does not change the API for retrieving configuration items, it degrades gracefully to the filesystem if not present and requires zero changes to the places which use the configuration items.

Part of the Cartalyst Arsenal & licensed [Cartalyst PSL](license.txt). Code well, rock on.

## Package Story

Package history and capabilities.

#### 18-Aug-14 - v1.1.0

- Store configuration on the database.
- Retrieve configurations from the database.
- Automatically caches configurations.
- Automatically flushes cache when a new config is set.

## Requirements

- PHP >=5.4

## Installation

Composite Config is installable with Composer. Read further information on how to install.

[Installation Guide](https://cartalyst.com/manual/composite-config#installation)

## Documentation

Refer to the following guide on how to use the Composite Config package.

[Documentation](https://cartalyst.com/manual/composite-config)

## Versioning

We version under the [Semantic Versioning](http://semver.org/) guidelines as much as possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backward compatibility bumps the major (and resets the minor and patch)
* New additions without breaking backward compatibility bumps the minor (and resets the patch)
* Bug fixes and misc changes bumps the patch

## Contributing

Please read the [Contributing](contributing.md) guidelines.

## Support

Have a bug? Please create an [issue](https://github.com/cartalyst/composite-config/issues) here on GitHub that conforms with [necolas's guidelines](https://github.com/necolas/issue-guidelines).

Follow us on Twitter, [@cartalyst](http://twitter.com/cartalyst).

Join us for a chat on IRC.

Server: irc.freenode.net
Channel: #cartalyst

Email: help@cartalyst.com
