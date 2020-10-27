# Minifier Changelog

## 2.0.4 - 2020-10-27
### Fixed
- Composer v2.0 compatibility ([#12](https://github.com/bitboxde/minifier/issues/12))


## 2.0.3 - 2019-08-20
### Fixed
- Fixed another bug that occured with autosuggest fields in Craft 3.* ([#5](https://github.com/bitboxde/minifier/issues/5) [#6](https://github.com/bitboxde/minifier/issues/6))

## 2.0.2 - 2019-08-20
### Fixed
- Fixed a bug that occured with autosuggest fields in Craft 3.2 ([#5](https://github.com/bitboxde/minifier/issues/5))

## 2.0.1 - 2019-02-28 [CRITICAL]
### Fixed
- Some files and some methods were missing on the last commit ([#3](https://github.com/bitboxde/minifier/issues/3))

## 2.0.0 - 2019-02-26 [ATTENTION]
:warning: Did you use a previous version (< 2.0.0), please see the **changed** log! There are a few
minor adjustments necessary in your project. But with these changes we are more flexible and more stable.

### Changed
- Base Path and Base URL usage. It's not the target min-directory any more, instead it's the default source directory.
  Have a look at the [documentation](https://github.com/bitboxde/minifier/blob/master/docs/README.md) for some examples.

### Added
- Performance improvement
- Events
- CSS preprocessor (less/Sass) solution via events
- More flexible options by registering files
- More configuration possibilities
- Using autosuggest field in Plugin Settings (since Craft CMS Version 3.1)
- Better documentation

## 1.0.2 - 2019-02-11
### Fixed
- Fixed a bug that crept in fixing another bug in Version 1.0.1 ([#2](https://github.com/bitboxde/minifier/issues/2))

## 1.0.1 - 2019-02-11
### Fixed
- Fixed a bug when loading files with http:// or https://
- Fixed a bug in "debug mode", when the minifier is disabled ([#2](https://github.com/bitboxde/minifier/issues/2))

## 1.0.0 - 2019-01-25
### Added
- Initial release
