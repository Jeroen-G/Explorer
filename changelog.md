# Changelog

All notable changes to `Explorer` will be documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## [3.15.0]

### Fixed
- Avoid overriding minimumShouldMatch on compound queries.

## [3.14.0]

### Added

- Add support for nested aggregations

## [3.13.0]

### Added

- Support Laravel and Scout 11.

## [3.12.0]

### Added

- Support for `missing` in sort order

## [3.11.0]

### Added
- Support for PHP8.3

## [3.10.0]

### Added
- The configurable logger may now be a channel name.

## [3.9.0]

### Changed
- Refactoring of rescoring and sorting.

### Fixed
- Non-bucket based aggregations throwing exception.

## [3.8.0]

### Added
- Updating the index alias is now done through a (queueable) job.
- Nested aggregations in the results.
- Option to enable a PSR-3 compliant logger.
- Allow custom order by (as a syntax object).

## [3.7.0]

### Added
- Support for Laravel Scouts's search callback.

### Fixed
- Only update aliases for the intended index.

## [3.6.0]

### Added
- PHPStan.
- Regex syntax.
- Default settings for every index via config.

### Changed
- Internals of the Scout builder and service provider.

### Fixed
- `where` and `whereIn` support.

## [3.5.0]

### Added
- QueryProperties with source filter and track total hits as first properties.
- Support for Laravel Scout 10.
- Support for `scout:delete-all-indexes` command.

### Fixed
- Bugs with deleting (aliased) indices.

### Changed
- (internal) service container bindings.

## [3.4.1]

### Added
- Support for Laravel v10

## [3.4]

### Added
- Add prefix lenght field on multimatch query.
- Support for PHP8.2.

### Fixed
- Range query error when starting at 0.
- Ensure that aliases are created before index is updated.

### Changed
- Index configuration split in direct and aliased configurations.

## [3.3.1]

### Fixes
- Bug returned class in Elastic client binding in the service provider.

## [3.3]

### Added
- With new config builder all connection options should be available.
- Parameters for the wildcard query syntax.

## Changed
- Set configuration for the Elasticsearch client using a builder.
- PHP 8.2 in the CI.
- Moved  `user` and `pass` from `explorer.connection`  to `explorer.connection.auth` `username` and `password`

## Fixed
- Index configs are not being skipped in update command.

## [3.2.1]

### Fixed
- SSL option for connections with ES8.

## [3.2.0]

### Added
- Configuration to use Basic Authentication (#99)
- Syntax Distance (#100)

### Fixed
- Scout vs Explorer index prefixes (#101)
- Scout flush command (#102)

## [3.0.1]

### Fixed
- Bug with running the update command for and index without an alias (#92)

## [3.0.0]

## Added
- Support for PHP 8.1
- Laravel Scout's prefix is added to the index name if present
- Max and Nested aggregations

## Changed
- Dropped support for PHP 7
- Dropped support for Laravel 7 and 8
- Removed deprecated `elastic:create` and `elastic:delete` commands
- DocumentAdapterInterface and IndexAdapterInterface have slightly changed

## [2.6.0]

### Added
- Configuration to use Basic Authentication (#99)

## [2.5.1]

### Fixed
- Fixed bug where index could not be created without settings (#81)

## [2.5.0]

### Added
- The new match_phrase query (#73)
- Ability to optionally set more parameters for a few queries (#68)
- Index aliases, useful for zero downtime indexing

### Changed
- Connection configuration now can include API ID and key (#74)

### Deprecated
- The `elastic:create` and `elastic:delete` are deprecated in favour of `scout:index` and `scout:delete-index`

## [2.4.1]

### Fixed
- Use lazy instead of cursor when mapping lazy collections

## [2.4.0]

### Added
- The scout:index and scout:delete commands are now implemented for Elasticsearch
- A DocumentAdapterInterface with an adapter that only deals with documents
- The Query String and Simple Query String syntax
- The ability to fake Elasticsearch responses, allowing integration tests with this package

### Changed
- The IndexAdapterInterface now only focuses on indices

### Deprecated
- If you rely on the old IndexAdapterInterface, use the DeprecatedElasticAdapterInterface instead of the IndexAdapterInterface to keep the functionality working as it previously was

## [2.3.0]

### Added
- Support for term aggregations

## [2.2.0]

### Added
- Wildcard syntax

### Fixed
- Pagination now works with the default Laravel Scout `paginate()` method

## [2.1.1]

### Added
- DisjunctionMax, Exists and Invert Query Syntaxes

### Changed
- Updated Laravel Scout support to include v9

## [2.0.0]

### Added
- Indices are now configured through a IndexConfiguration class and repository
- Both Match and MultiMatch queries may now specify a 'fuzziness', the default stays 'auto'
- Using the `field()` on the search builder you may now define specifically which field(s) should be retrieved for the documents
- New function score compound queries that can replace the default boolean compound query
- A static `debug` method to help you with the last executed query
- Laravel Scout's `take()` method can be used to set the max amount of results
- Text analysis (analyzers, tokenizers, filters, etc.) is now possible through index settings.
- Prepare (parts of) your data before letting Elasticsearch index it.

### Changed
- Sorting now uses the default Scout `orderBy()` method
- The MultiMatch now accepts a `fields` array with the fields to search in
- The Engine now uses Elastic's bulk operations to speed up updates of models

### Fixed
- Running `scout:flush` now actually deletes the contents of an index

## [1.2.0]

### Added
- Added the `newCompound()` function to customize the compound query

### Fixed
- Boosts may now completely be left out
- Console commands now use the configured connection
- Client used to boot with three connections instead of one

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
