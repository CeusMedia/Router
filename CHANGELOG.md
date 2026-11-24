# Changelog for CeusMedia/Router

## Version 0.6.2
- Make ext-memcache optional (suggest): only needed for the Memcache route source, which now throws a clear exception if the extension is missing.
- Add meta information on routes (`meta` in JSON route files, `Route::getMeta()`, `setMeta()`, `hasMeta()`), carried for consumers like a REST server, e.g. `{"public": true, "rateLimit": "auth"}`.
- JSON route files accept roles as list, besides comma separated string.
- Fix: Loading routes from a source with autosave (e.g. `Router::loadRoutesFromJsonFile()`) no longer rewrites the source while reading it.
- Fix: JSON route source saves roles and meta, and omits unknown mode.
- Fix: Resolver matches static pattern parts literally (no regex interpretation of `.`, `+`, …) and detects a mismatch on the last pattern part. Patterns relying on regular expressions in static parts no longer match.


## Version 0.6.1
- Add memory route registry source, usable for testing or high performance use cases.
- Improve stability of route factory on priority, controller, action and roles (list or comma separated string).
- Improve unit tests (62% coverage) and code documentation.


## Version 0.6.0
- PHP 8.3 support, drop support for PHP < 8.3.
- Migrate to support CeusMedia/Common 1.1.x and CeusMedia/Cache 0.7.x.


## Version 0.5
- PHP 8.1 support and migration.
- Migrate to support CeusMedia/Common 1.0.x.
- Use PHPUnit 10.1.
- Support CeusMedia/Cache 0.6.x.
- Reach maximum PHPStan level.
- Move tool config files to util.
- Add badges and show examples in readme.
- Use strict types.
- Apply PHPStan also on tests and demos.
- Update tests.
- Update copyright year.


## Version 0.4
- Extend route model by roles.
- Migrate to support CeusMedia/Common 0.9.x
- Fix bug in merging custom and default source/router options.
- Add priority property on route.
- Improve code quality.
- Reach maximum PHPStan level.
- Short array syntax.
- Fix PHPStan to PHP 7.4.

## Version 0.3
- Add this changelog.
- Extend registry by method to index routes by controller.
- Allow multiple HTTP methods in route definition, separated by comma.
- Improve path part replacements.

## Version 0.2
- Throw ResolverException if route is not resolvable.
- Extend route by arguments to be applied later.
- Add code doc.
- Remove format classes, which are now in CeusMedia/REST.
- Update composer, make and .gitignore file.
- Add PHPUnit file.
- Add DocCreator file.

## Version 0.1
- Fix namespaces.
- Add formats to channel request and response MIME types.
- Add test bootstrap.
- Update make file and .gitignore.
- Set file permissions.
- Add .gitignore.
- Initial commit.
