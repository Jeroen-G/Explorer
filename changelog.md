# Changelog

All notable changes to `Explorer` will be documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## Added
- Added the `newAggregate()` function to customize the aggregate query builder

### Fixed
- Boosts may now completely be left out

### Changed
- BoolQueries can now also be nested

## [1.1.0]

### Added
- Ability to query nested property types

### Fixed
- Boosts are now null by default

## [1.0.1]

### Fixed
- The boost field in the Range query was not in the right place
- The builder used the words must, should and filter plural instead of singular

### Updated
- Updated dev dependency Infection to 0.20

## [1.0.0]

### Added
- Everything
