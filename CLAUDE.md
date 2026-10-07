# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Lineage

This repository, **Atk14MiniSkelet**, is the base of a chain of ATK14 application skeletons, each building on the previous one: `Atk14MiniSkelet` → `Atk14Skelet` → `Atk14Catalog` → `Atk14Eshop` → actual client applications. Conventions, structure and fixes introduced here flow forward into every one of those. If you're reading this file in a downstream repository, the directory layout, routing conventions, and ATK14 idioms described below originated here and should still apply unless explicitly overridden locally.

This file is itself one of the things that flows downstream, usually via a regular merge from the parent skeleton. Keep that in mind when editing it:
- Describe conventions and mechanisms, not an exhaustive snapshot of current contents (e.g. "the admin nav is built from a declarative `$items` list" rather than "the admin nav has exactly these three entries") — a downstream app's actual controllers, migrations, and models will diverge over time, and the description should keep holding up anyway.
- Add downstream-specific guidance (a real app's business domain, a child skeleton's added features) in new sections rather than rewriting the inherited ones, so merges from upstream stay conflict-free by default.
- If a merge *does* conflict on this file, that's a useful signal that local reality and the upstream description diverged — resolve it by hand and update the wording, don't just take one side automatically.

## What this is

A minimal, runnable ATK14 application ("skeleton") providing just enough to bootstrap a real app: user registration, login/logout, password recovery, a small admin section, and front-end asset tooling (Gulp + BrowserSync). It is not itself a product — it exists to be cloned/forked into real applications. The ATK14 framework itself lives as a git submodule in `atk14/` (https://github.com/atk14/Atk14.git); application code lives alongside it following ATK14's MVC conventions.

## Setup and commands

```bash
git submodule init && git submodule update   # fetch the atk14 framework submodule
composer install
./scripts/create_database
./scripts/migrate
```

After cloning this skeleton to start a new app, run `./local_scripts/update_project_name <appname>` (or with no argument, to infer the name from the current directory name) — it rewrites the `_test`/`_devel`/`_production` db/username suffixes in `config/database.yml` and `ATK14_HTTP_HOST`/`ATK14_APPLICATION_NAME` in `config/settings.php`. This is the actual "fork the skeleton into a real app" step; do it before anything else in a freshly cloned repo.

A seeded admin user (`login: admin`, `password: admin`, `is_admin = true`, id 1) exists after migrations (`db/migrations/0001_users.sql` + `0002_reset_admins_password_migration.php`) — use it to sign in to `/admin/` locally. `RemoteTestsController::admin_default_password()` (see below) exists specifically to fail a production health check if this default password was never changed.

Run the dev server: `./scripts/server` (defaults to `http://localhost:8000/`, optional port/address as first arg).

Front-end assets (Gulp):
```bash
npm install -g gulp && npm install
gulp && gulp admin        # initial build of public site + admin assets
gulp serve                # serve/watch public site assets with BrowserSync
gulp lint                 # ESLint for public site JS (gulpfile.js)
gulp lint-admin           # ESLint for admin JS (gulpfile-admin.js)
```

Database:
```bash
ATK14_ENV=test ./scripts/create_database
ATK14_ENV=test ./scripts/migrate         # applies pending db/migrations/* (.sql or .php)
./scripts/migrate --list                 # list migrations / status
./scripts/dbconsole                      # open a DB console for the current env
./scripts/dump_db                        # dump schema + data (backup); ATK14_ENV=PRODUCTION ./scripts/dump_db -t users for one table
./scripts/dump_dbschema                  # dump schema only
echo 'y' | ATK14_ENV=test ./scripts/destroy_database_objects && ATK14_ENV=test ./scripts/migrate  # wipe and recreate the test db (DEVELOPMENT/TESTING env only)
```

Tests (ATK14's own tester, not PHPUnit):
```bash
./scripts/run_all_tests                  # runs every test/* directory containing initialize.php
cd test/controllers && ../../scripts/run_unit_tests tc_users       # run one test file (TcUsers in tc_users.php)
cd test/models && ../../scripts/run_unit_tests tc_application_model tc_invalid_password_attempt  # run a few
```
`run_all_tests` auto-applies pending `ATK14_ENV=TEST` migrations before running. Test suites live under `test/{app,controllers,controllers/admin,controllers/api,models,fields,helpers,lib}`, each with its own `initialize.php`. A test case file `tc_foo.php` must define a class `TcFoo`.

Fixtures (`test/fixtures/*.yml`, see `test/fixtures/README.md`) seed test data declaratively — a YAML file named e.g. `users.yml` defines named records (`rambo:`, `rocky:`, …) for model `User`. A test case opts in via a `@fixture` docblock annotation (one per line for multiple fixtures) and gets the loaded records as `$this->users["rambo"]`, etc. — see `test/controllers/tc_logins.php` or `test/fixtures/users.yml` for real examples.

Interactive console (like a Ruby on Rails console — loads the full app environment and models for ad-hoc use):
```bash
./scripts/console
echo '$u = User::FindFirst(); var_dump($u->toArray());' | ./scripts/console   # non-interactive mode
```

Inspect effective configuration:
```bash
./scripts/dump_settings                  # list all application constants
./scripts/dump_settings DEFAULT_EMAIL    # inspect a single constant
./scripts/recognize_route http://atk14miniskelet.localhost/some/path/   # see which route/controller/action matches a URI
```

Background jobs (`robots/`, see `robots/README.md`): classes `FooRobot extends ApplicationRobot` (e.g. `sessions_cleanup_robot.php`, `vacuum_analyze_robot.php`, `invalid_password_attempts_cleanup_robot.php`) run via the framework's `./scripts/robot_runner <name>`, accepting the name in several forms (`sessions_cleanup`, `sessions_cleanup_robot`, `SessionsCleanupRobot`, …). In production these are wired into cron via `local_scripts/robots_regular|robots_daily|robots_weekly` (each just a list of `./scripts/robot_runner ...` calls) — see `local_scripts/README.md` for the example crontab. Robots take out lock files in `robots/lock/`; `RemoteTestsController::stale_locks()` monitors those for staleness. Don't confuse this `robots/` directory with `test/` — it has nothing to do with testing.

Deployment, driven by `config/deploy.yml` (per-stage server/user/directory, `before_deploy`/`after_deploy` hooks, rsync paths):
```bash
./scripts/deploy              # deploy to the first stage in config/deploy.yml (production)
./scripts/deploy production
./scripts/deploy preview      # any other stage defined in config/deploy.yml
./scripts/deploy -l           # list available stages
```

## Architecture

**Request flow:** `public/index.php` → `dispatcher.php` → `atk14/load.php` boots the framework → `Atk14Dispatcher::Dispatch()` resolves the request through the routers in `config/routers/load.php`, then instantiates the matched `*_controller.php` under `app/controllers/`.

**Routing (`config/routers/`):** Routers are plain PHP classes autoloaded by filename (class `FooBarRouter` ↔ file `foo_bar_router.php`), registered in `config/routers/load.php` in the order they should be tried:
- `AdminRouter` (`admin_router.php`) — namespace `"admin"`, currently empty; add admin-specific SEF routes here.
- `ApplicationRouter` (`application_router.php`) — namespace `""`; hand-written, search-engine-friendly routes (`/sign-in/`, `/sign-up/`, `/recovery/<token>`, sitemap/robots routes, etc.). Add new human-friendly URLs here, in order, before the generic fallback.
- `GenericRouter` (`generic_router.php`) — **must stay last**. Provides the catch-all patterns every ATK14 app needs: `/`, `/<lang>/`, `/<lang>/<controller>/`, `/<lang>/<controller>/<action>/`. Don't add specific routes here; add them to `ApplicationRouter` (or a namespace-specific router) instead, since routers are matched in registration order.

**Controllers (`app/controllers/`):** `ApplicationBaseController` (extends `Atk14Controller`) holds cross-cutting concerns (breadcrumbs, `error404`/`error403`, before/after filter wiring, transaction begin/end). `ApplicationController` extends it for the public namespace. Namespaced controllers live in subdirectories matching the router namespace, e.g. `app/controllers/admin/`, `app/controllers/api/`.

`admin/admin.php` defines `AdminController`, the base for all admin-namespace controllers (`main_controller.php`, `users_controller.php`, `password_recoveries_controller.php`, …). Its `_application_before_filter()`:
- enforces `logged_user->isAdmin()`, redirecting anonymous users to login;
- builds the admin section navigation (`Menu14`) and breadcrumbs from a declarative `$items` list of `[label, target]` pairs. `target` may be a bare controller name (implies `.../index`), an explicit `"controller/action"` string, a comma-separated list of controllers sharing one nav entry, or an absolute path starting with `/` (used verbatim, never marked "active" since it has no corresponding controller). See the code comments there before changing this logic — the leading-slash check exists specifically to avoid `strpos() === 0` being falsy in PHP.

`RemoteTestsController` (`app/controllers/remote_tests_controller.php`) is an HTTP health-check endpoint for external monitoring (e.g. Nagios), not a test suite despite living next to `test/`: each public method returns HTTP 200 (pass) or 500 (fail) via `_assert_true()`/`_fail()`, and `index()` lists all of them by reflecting on its own source. Existing checks include `disk_space`, `stale_locks` (robots/lock/ freshness, see Background jobs above), and `admin_default_password` (fails if the seeded admin/admin login still works — see Setup above).

CRUD-heavy admin controllers mix in `TraitCrudActions` (`app/controllers/trait_crud_actions.php`) for generic `_index`/`_create_new`/etc. helpers driven by an `$options` array (`class_name`, `searching_in`, `sorting_by`, `conditions`, …) rather than reimplementing listing/search/sort per controller.

**Views (`app/views/<namespace or controller>/`), layouts (`app/layouts/`), helpers (`app/helpers/`):** Smarty-based (`.tpl`). Custom Smarty blocks/functions/modifiers live in `app/helpers/` as `block.*.php`, `function.*.php`, `modifier.*.php` (e.g. `function.admin_menu.php` renders the per-object admin dropdown menu used across detail views). `app/layouts/admin/` and `app/layouts/rest_api/` hold layout variants for those namespaces.

**Models (`app/models/`):** Plain ActiveRecord-style classes extending `Atk14` conventions; shared behaviors are implemented as traits/mixins (`translatable.php`, `rankable.php`, `trait_get_instance_by_code.php`) rather than deep inheritance.

Two distinct password-hashing mechanisms coexist, don't confuse them: `User` hashes its login password via `MyBlowfish::Filter()` (package `yarri/my-blowfish`, requires the `CRYPT_BLOWFISH` PHP flag asserted in `config/requirements.yml`) transparently inside `setValues()`/`CreateNewRecord()`, checked via `User::isPasswordCorrect()`; one-time passwords (`app/models/one_time_password.php`, used for recovery/2FA-style flows, scoped by `purpose` + `object_key`) instead hash via PHP's own `password_hash()`. Brute-force login throttling is `InvalidPasswordAttempt::IsRemoteAddressBlocked($ip)` — an exponential-backoff lockout (5 attempts allowed per round, 5–60 min escalating delay, 2h lookback window; `MAX_INVALID_LOGIN_ATTEMPTS` setting) — reused for any `purpose`/`object_key`-scoped throttling, not just the login form.

Custom form fields (`app/fields/`) follow ATK14's `*Field extends ...Field` convention. Notably `ObjectField` (base for `UserField`, etc.) auto-derives both the related model class (`UserField` → `User`) and an autocomplete suggestion endpoint (`UserField` → `api/suggestions/users`) purely from the field's own class name; the matching controller side is the shared `_suggest()` helper in `app/controllers/api/suggestions_controller.php`. To add an autocomplete picker for a new model, this is the pair of places to touch.

**Mailer (`app/controllers/application_mailer.php`, `app/views/mailer/*.tpl`):** one method per email, e.g. `notify_user_registration($user)` sets `to`/`subject`/`tpl_data` and implicitly renders `app/views/mailer/notify_user_registration.html.tpl` (called via `$this->mailer->notify_user_registration($user)`, proxied through `Atk14MailerProxy`). In `DEVELOPMENT`, mail is logged instead of actually sent. `_after_render()` auto-derives the plain-text body from the HTML body via `Html2Text` when no explicit plain body was set, stripping anything wrapped in `<!-- header -->…<!-- /header -->` / `<!-- footer -->…<!-- /footer -->` HTML comments first.

**Database (`db/migrations/`, `config/database.yml`):** Numbered, sequentially-applied `.sql` or `.php` migration files (`0001_users.sql`, `0002_reset_admins_password_migration.php`, …). `zz01`/`zz02` suffixes mark later, corrective migrations against an earlier-numbered one. `application_migration.php` is the shared base class for PHP migrations. `config/database.yml`'s `production` password is not a plaintext committed secret but a PHP expression evaluated at load time (`<?=sha1(SECRET_TOKEN."...")?>`) — the pattern for deriving production secrets from `SECRET_TOKEN` instead of committing them.

**i18n (`config/locale.yml`, `locale/`):** Supported languages and the default one are declared in `config/locale.yml` (first entry = default; `fallback` lets one language inherit translations from another). Routes carry a `<lang>` segment (see `GenericRouter`); `$this->default_lang` is available in router `setUp()`.

**Settings (`config/settings.php`):** Application-wide constants via `definedef(...)` (define-if-not-already-defined), including feature toggles like `USER_REGISTRATION_ENABLED`, `INVITATION_CODE_FOR_USER_REGISTRATION`, `USING_BOOTSTRAP4`. Framework defaults are in `atk14/default_settings.php`; use `./scripts/dump_settings` rather than grepping both files.

**Local overrides (`local_config/`):** Everything under `local_config/` is gitignored (not version-controlled) — it's per-environment/per-developer config. A file here (e.g. `local_config/settings.php`) takes precedence over the same-named file in `config/` (`atk14/load.php` loads `local_config/settings.php` instead of `config/settings.php` when it exists). The convention is to end `local_config/settings.php` with `require(__DIR__ . "/../config/settings.php");` so local overrides layer on top of (before) the tracked defaults rather than replacing them entirely.

**REST API (`app/controllers/api/`, `app/controllers/application_rest_api.php`, `app/forms/api/`, `app/views/shared/rest_api/`):** A separate namespace/layout for JSON endpoints, following the same controller/form conventions as the HTML side. `ApplicationRestApiController` auto-generates human-readable docs per action from doc-comments; set `$doc_basic_auth` (string `"user:pass"` or an array keyed by regex) on a controller to put that generated documentation behind HTTP basic auth — separate from whatever auth the actual API endpoints use.

**Error reporting (`lib/load.php`):** when the Tracy library is available and not in the `TEST` env or CLI, `Tracy\Debugger::enable(PRODUCTION, log/, …)` is wired up — in production, uncaught PHP errors/exceptions are logged under `log/` and emailed to `ATK14_ADMIN_EMAIL` (`config/settings.php`); outside production Tracy's debug bar is shown instead. `lib/` is also where small non-Composer helper libraries (e.g. `lib/head_tags/`) and the Composer autoloader itself get required from.

**Forms (`app/forms/`):** One form class per controller action pattern (`app/forms/users/create_new_form.php`-style), mirroring the `app/controllers/` namespace layout; `application_form.php` / `rest_api_form.php` are the shared base classes.
