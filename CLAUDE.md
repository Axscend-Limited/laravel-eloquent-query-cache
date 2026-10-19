# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Axscend's fork of `renoki-co/laravel-eloquent-query-cache`, published as `axscend/laravel-eloquent-query-cache`. It `replace`s the upstream package name and keeps the `Rennokki\QueryCache` namespace, so consuming apps change nothing. Keep the fork's changes against upstream listed in the README's "Axscend fork" block. User docs live on upstream's Gitbook, not in this repo.

## Commands

```bash
composer install
vendor/bin/phpunit                                  # full suite (array cache driver by default)
vendor/bin/phpunit tests/GetTest.php                # one file
vendor/bin/phpunit --filter test_get_with_columns   # one test
CACHE_DRIVER=file vendor/bin/phpunit                # driver without tag support
```

CI runs the suite twice, with `CACHE_DRIVER=array` and `CACHE_DRIVER=file`. Run both before calling a change done: tag-dependent behaviour differs, and tests branch on `driverSupportsTags()` in `tests/TestCase.php`.

There is no linter in the repo. Style is enforced by StyleCI (`.styleci.yml`, Laravel preset).

## How it works

- `Traits\QueryCacheable` goes on a model. It overrides `newBaseQueryBuilder()` to return `Query\Builder` instead of Laravel's base query builder, then copies the model's cache settings onto it.
- Each setting can come from a property (`$cacheFor`, `$cacheTags`, `$cachePrefix`, `$cacheDriver`, `$cacheUsePlainKey`) or a `...Value($builder)` method. The method wins because it is applied second.
- The builder starts with caching off (`dontCache()`). Caching only happens once `cacheFor` is set to a non-null value, on the model or per query.
- `Query\Builder` overrides `get()` only. `first`, `count`, `paginate` and the rest all route through `get()`. `useWritePdo()` turns caching off.
- `Traits\QueryCacheModule` holds the cache logic: the key is `{prefix}:sha256(connection + method + SQL + bindings)`, or the plain string with `withPlainKey()`. Tags are the query tags plus the model's base tags, which default to the model class name.
- `FlushQueryCacheObserver` is attached when a model sets `protected static $flushCacheOnUpdate = true`. It flushes the tags from `getCacheTagsToInvalidateOnUpdate()` on model events. The `belongsToMany*` / `morphToMany*` handlers only fire if the app uses `chelout/laravel-relationship-events`, which is a dev dependency here.
- Flushing needs a driver with tags. On `file` / `database`, `flushQueryCache()` returns `false`.

## Tests

- Orchestra Testbench with SQLite at `tests/database/database.sqlite`, truncated in `setUp()`.
- Fixtures are in `tests/Models`, `tests/database/migrations` and legacy factories in `tests/database/factories` (`laravel/legacy-factories`).
- Many tests take `strictModeContextProvider`, which runs them with `Model::preventAccessingMissingAttributes` on and off.
