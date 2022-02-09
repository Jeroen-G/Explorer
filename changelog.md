# Changelog

All notable changes to `Explorer` will be documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

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
