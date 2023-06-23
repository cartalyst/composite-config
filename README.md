# Composite Config

[![Build Status][icon-travis]][link-travis]

Our composite config package enhances `illuminate/config` to allow configuration items to be placed within a database whilst cascading back to the filesystem.

This is super useful for building user interfaces that facilitate editing configuration for an app. Because it does not change the API for retrieving configuration items, it degrades gracefully to the filesystem if not present and requires zero changes to the places which use the configuration items.

Part of the Cartalyst Arsenal & licensed [Cartalyst PSL](LICENSE). Code well, rock on.

## Version Matrix

Version | Laravel   | PHP Version
------- | --------- | ------------
7.x     | 10.x      | >= 8.1
6.x     | 9.x       | >= 8.0
5.x     | 8.x       | >= 7.3
4.x     | 7.x       | >= 7.2.5
3.x     | 6.x       | >= 7.2
2.x     | 5.0 - 5.8 | >= 5.4.0
1.x     | 4.0 - 4.2 | >= 5.3.0

## Documentation

Reader-friendly documentation can be found [here][link-docs].

Using the package, but you're stuck? Found a bug? Have a question or suggestion for improving this package? Feel free to create an issue on GitHub, we'll try to address it as soon as possible.

## Contributing

Thank you for your interest, here are some of the many ways to contribute.

- Check out our [contributing guide](/.github/CONTRIBUTING.md)
- Look at our [code of conduct](/.github/CODE_OF_CONDUCT.md)

## Security

If you discover any security related issues, please email help@cartalyst.com instead of using the issue tracker.

## License

This software is released under the [Cartalyst PSL](LICENSE) License.

[link-docs]:   https://cartalyst.com/manual/composite-config
[link-travis]: https://travis-ci.com/cartalyst/composite-config

[icon-travis]: https://travis-ci.com/cartalyst/composite-config.svg?token=LAut3LMbmBFi3T9j45FH&branch=7.x
