# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.0.0 - 2022-05-30

### Added

- [#9](https://github.com/eventjet/eventjet-asset-manager/pull/9) announces support for PHP 8 in `composer.json`

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- **BC Break:** [#9](https://github.com/eventjet/eventjet-asset-manager/pull/9) removes dependency
  on `laminas/diactoros`.
  Therefore, an implementation of `Psr\Http\Message\ResponseFactoryInterface` has to be provided to
  `Eventjet\AssetManager\Service\AssetManager`.

### Fixed

- Nothing.

## 0.1.3 - 2021-10-21

### Added

- [#8](https://github.com/eventjet/eventjet-asset-manager/pull/8) adds support for PHP 8

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
