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

CI runs the suite twice, with `CACHE_DRIVER=array` and `CACHE_DRIVER=file`. Run both before calling a change done: tag-dependent behaviour differs, and tests branch on `driverSupportsTags()` in `tests/TestCase.php`. The env var feeds `cache.default`, not `cache.driver`; Laravel ignores the latter.

Supports Laravel 12.8+ and 13. `composer install` gives Laravel 13; to test on 12 without editing `composer.json`:

```bash
composer update --with "laravel/framework:^12.8" -W
```

There is no linter in the repo. Style is enforced by StyleCI (`.styleci.yml`, Laravel preset).

## How it works

- `Traits\QueryCacheable` goes on a model. It overrides `newBaseQueryBuilder()` to return `Query\Builder` instead of Laravel's base query builder, then copies the model's cache settings onto it.
- Each setting can come from a property (`$cacheFor`, `$cacheTags`, `$cachePrefix`, `$cacheDriver`, `$cacheUsePlainKey`) or a `...Value($builder)` method. The method wins because it is applied second.
- The builder starts with caching off (`dontCache()`). Caching only happens once `cacheFor` is set to a non-null value, on the model or per query.
- `Query\Builder` overrides `get()`, `pluck()` and `exists()`. `first`, `value`, `count`, `paginate` and the rest route through `get()`; `doesntExist` goes through `exists()`. Laravel's `pluck()` and `exists()` skip `get()`, which is why they need their own overrides. `useWritePdo()` turns caching off.
- `cacheFor()`: null or 0 turns caching off, a negative number (`cacheForever()` uses -1) caches forever. It accepts seconds, any `DateTimeInterface` or a `DateInterval`.
- `Traits\QueryCacheModule` holds the cache logic: the key is `{prefix}:sha256(connection + method + SQL + bindings)`, or the plain string with `withPlainKey()`. Tags are the query tags plus the model's base tags, which default to the model class name.
- `FlushQueryCacheObserver` is attached when a model sets `protected static $flushCacheOnUpdate = true`. It is registered in `whenBooted()`: calling `observe()` directly during boot throws on Laravel 13. It flushes the tags from `getCacheTagsToInvalidateOnUpdate()` on model events. The `belongsToMany*` / `morphToMany*` handlers only fire if the app uses `chelout/laravel-relationship-events`, which is a dev dependency here.
- On a store without tags (`file`, `database`, `dynamodb`), `Repository::tags()` throws, and `flushQueryCacheWithTag()` falls back to `$cache->flush()`: it wipes the **whole store**, not just query entries. This is deliberate; without it, cached results would go stale after updates.

## Tests

- Orchestra Testbench with SQLite at `tests/database/database.sqlite`, truncated in `setUp()`.
- Fixtures are in `tests/Models`, `tests/database/migrations` and legacy factories in `tests/database/factories` (`laravel/legacy-factories`).
- Many tests use `#[DataProvider('strictModeContextProvider')]`, which runs them with `Model::preventAccessingMissingAttributes` on and off. `TestCase::setUp()` reads the value via `providedData()`, so the test's `bool $strict` parameter is unused but required.
- Expected cache keys are plain strings in the tests. Laravel 13 quotes the aggregate alias, so count/paginate keys use `$this->aggregateAlias()`.
- `tests/` and `phpunit.xml` are `export-ignore`d, so a `git archive` copy has no tests.
