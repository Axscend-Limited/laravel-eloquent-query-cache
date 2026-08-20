> ## Axscend fork
>
> This is Axscend's fork of [renoki-co/laravel-eloquent-query-cache](https://github.com/renoki-co/laravel-eloquent-query-cache),
> maintained because upstream has had no code commits since March 2025 while
> fixes sit in open PRs.
>
> Installed as `axscend/laravel-eloquent-query-cache`. It `replace`s the
> original package name and keeps the `Rennokki\QueryCache` namespace, so no
> application code changes are needed.
>
> Changes against upstream `master`:
>
> - Explicit nullable parameter types, fixing the PHP 8.4 implicit-nullable
>   deprecations (upstream PR [#236](https://github.com/renoki-co/laravel-eloquent-query-cache/pull/236)).
> - Laravel 13 support in `composer.json` (the `composer.json` half of upstream
>   PR [#243](https://github.com/renoki-co/laravel-eloquent-query-cache/pull/243);
>   its CI workflow changes were skipped as they are invalid YAML).
>
> To pull in upstream changes if it is ever revived:
> `git remote add upstream https://github.com/renoki-co/laravel-eloquent-query-cache.git && git rebase upstream/master`

Laravel Eloquent Query Cache
============================

![CI](https://github.com/renoki-co/laravel-eloquent-query-cache/workflows/CI/badge.svg?branch=master)
[![codecov](https://codecov.io/gh/renoki-co/laravel-eloquent-query-cache/branch/master/graph/badge.svg)](https://codecov.io/gh/renoki-co/laravel-eloquent-query-cache/branch/master)
[![StyleCI](https://github.styleci.io/repos/223236785/shield?branch=master)](https://github.styleci.io/repos/223236785)
[![Latest Stable Version](https://poser.pugx.org/rennokki/laravel-eloquent-query-cache/v/stable)](https://packagist.org/packages/rennokki/laravel-eloquent-query-cache)
[![Total Downloads](https://poser.pugx.org/rennokki/laravel-eloquent-query-cache/downloads)](https://packagist.org/packages/rennokki/laravel-eloquent-query-cache)
[![Monthly Downloads](https://poser.pugx.org/rennokki/laravel-eloquent-query-cache/d/monthly)](https://packagist.org/packages/rennokki/laravel-eloquent-query-cache)
[![License](https://poser.pugx.org/rennokki/laravel-eloquent-query-cache/license)](https://packagist.org/packages/rennokki/laravel-eloquent-query-cache)

Laravel Eloquent Query Cache brings back the `remember()` functionality that has been removed from Laravel a long time ago.
It adds caching functionalities directly on the Eloquent level, making use of cache within your database queries.

## 🤝 Supporting

**If you are using one or more Renoki Co. open-source packages in your production apps, in presentation demos, hobby projects, school projects or so, sponsor our work with [Github Sponsors](https://github.com/sponsors/rennokki). 📦**

[<img src="https://github-content.s3.fr-par.scw.cloud/static/37.jpg" height="210" width="418" />](https://github-content.renoki.org/github-repo/37)

## 📃 Documentation

[The entire documentation is available on Gitbook 🌍](https://rennokki.gitbook.io/laravel-eloquent-query-cache/)

## 🐛 Testing

``` bash
vendor/bin/phpunit
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒  Security

If you discover any security related issues, please email alex@renoki.org instead of using the issue tracker.

## 🎉 Credits

- [Alex Renoki](https://github.com/rennokki)
- [All Contributors](../../contributors)
