# Composite Config

[![Build Status](http://ci.cartalyst.com/build-status/svg/10)](http://ci.cartalyst.com/build-status/view/10)

Our composite config package enhances `illuminate/config` to allow configuration items to be placed within a database whilst cascading back to the filesystem.

This is super useful for building user interfaces that facilitate editing configuration for an app. Because it does not change the API for retrieving configuration items, it degrades gracefully to the filesystem if not present and requires zero changes to the places which use the configuration items.

Part of the Cartalyst Arsenal & licensed [Cartalyst PSL](LICENSE). Code well, rock on.

## Documentation

Reader-friendly Documentation can be found here. [Composite Config Manual](https://cartalyst.com/manual/composite-config).

Raw files can be found via this projects docs/version branch.

- [2.0](https://github.com/cartalyst/composite-config/tree/docs/2.0)
- [1.0](https://github.com/cartalyst/composite-config/tree/docs/1.0)

## Changelog

Important versions listed below. Refer to the [Changelog](CHANGELOG.md) for a full history of the project.

- [2.0](CHANGELOG.md) - 2015-02-24
- [1.0](CHANGELOG.md) - 2013-05-28

## Support

The following support channels can be used for contact.

- [Twitter](https://cartalyst.com/@twitter)
- [Email](mailto:help@cartalyst.com)

Bug reports, feature requests, and pull requests can be submitted by following our [Contribution Guide](CONTRIBUTING.md).

## Contributing & Protocols

- [Versioning](CONTRIBUTING.md#versioning)
- [Coding Standards](CONTRIBUTING.md#coding-standards)
- [Pull Requests](CONTRIBUTING.md#pull-requests)

## License

This software is released under the [Cartalyst PSL](LICENSE) License.

© 2011-2015 Cartalyst LLC, All rights reserved.
