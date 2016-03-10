# Changelog

This project adheres to [Semantic Versioning](http://semver.org/).

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.0.1 - TBD

### Added
- composer scripts: `check` `test` `cs-check` `cs-fix`;
- `composer.lock` file is now committed in;
- travis build now runs against `lowest`, `locked` and `latest` dependencies. 

### Changed
- `definitions` config can now be defined as topmost array key in the module configuration.

### Fixed
- Multiple definitions can now be configured correctly.

## 1.0.0 - 2015-12-17

### Added
- PHP-CS-Fixer configuration
- Travis CI configuration

### Changed
- Minor updates in README.md

### Fixed
- Coding styles


## 1.0.0-rc - 2015-12-12

### Added
- Add the ability to configure HTMLPurifier via filter options ([#3](https://github.com/Soflomo/Purifier/pull/3/))
- The default `HTMLPurifier_Config` instance is now available as a service via its FQCN.
- Support for HTMLPurifier custom definitions ([#1](https://github.com/juriansluiman/Soflomo-Purifier/pull/1))

### Changed
- Updated the docs in README.md
- **[BC-BREAK]** Switched to PSR-4 and refactored class hierarchy
- **[BC-BREAK]** Converted all arrays to php 5.4 short array syntax
- **[BC-BREAK]** Bumped minimum PHP version to 5.5 (as required by zend-filter)
- **[BC-BREAK]** Removed zendframework monolithic dependency and bumped zend-filter minimum version to 2.5 ([#5](https://github.com/Soflomo/Purifier/pull/5), [#6](https://github.com/Soflomo/Purifier/pull/6))

### Removed
- **[BC-BREAK]** Loading the module via `Zend\Loader` is no longer possible. Composer autoloader is now required.


## 0.1.1 - 2013-04-23

### Changed
- Composer metadata

## 0.1.0 - 2013-04-23

**Initial release**
