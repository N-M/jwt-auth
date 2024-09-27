# Changelog

All notable changes to this project will be documented in this file, in reverse
chronological order by release.

## [2.0](https://github.com/jimtools/jwt-auth/compare/1.0.0...2.0.0) - 2024-09-27

### Changed

2.0 released major rework of the package works, see [UPGRADING](UPGRADING)

- Fix issues with CI/CD
- Updated README to better reflect breaking changes
- added replace in composer.json

## [1.0.2](https://github.com/jimtools/jwt-auth/compare/1.0.1...1.0.2) - 2024-09-14

### Added

- Improved type coverage ([#6](https://github.com/JimTools/jwt-auth/pull/6))
- Improved docs ([#5](https://github.com/JimTools/jwt-auth/pull/5))

## [1.0.1](https://github.com/jimtools/jwt-auth/compare/1.0.0...1.0.1) - 2024-03-24

## 1.0.0 - 2024-02-19

Inital fork of the package

- Minimum PHP 8.1+
- Namespace changed
- Dropped support for PSR-7 (double pass)
- Options moved into class
- Secrets moved into class
- Logger removed
- removed error handler
- Before and After handlers changed
