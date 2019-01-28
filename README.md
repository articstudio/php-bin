# php-bin
PHP bin helpers.

**Install**
```
composer require articstudio/php-bin
```

**Usage**

For launch a interactive menu execute (in the root folder of project):
```
php vendor/bin/phpbin
```
If you would to execute in a command line environment, the pattern of execution is:
```
php vendor/bin/phpbin name-menu:name-task:name-subtask [parameter1] [parameter2]
```

Examples:

**Add a subtree:**
`
php vendor/bin/phpbin git:subtree:add [repository/subtree1]
`

**Push a subtree:**
`
php vendor/bin/phpbin git:subtree:push [repository/subtree1]
`

**Install package to module:**
`
php vendor/bin/phpbin composer:install [package-name] [module-name] [d/D]
`


This package is composed by three big menus:

## Git helpers: 
Provides some commands to manage a simple tasks for git subtrees.

By default this command take a set of subtrees who indicate in `composer.json`, attribute "config: subtree{}".
But you can type a name of subtree to add manually.

- Launch git menu
`
php vendor/bin/phpbin git
`

- Git add subtree
`
php vendor/bin/phpbin git:subtree:add [repository/subtree1]
`

- Git pull subtree
`
php vendor/bin/phpbin git:subtree:pull [repository/subtree1]
`

- Git push subtree
`
php vendor/bin/phpbin git:subtree:push [repository/subtree1]
`

- Git remove subtree
`
php vendor/bin/phpbin git:subtree:remove [repository/subtree1]
`

- Git check local subtrees with composer.json subtrees
`
php vendor/bin/phpbin git:subtree:check
`
## PHP helpers:

Provides some commands to manage a php tasks:

- PHP lint (Syntax checker)
`
php vendor/bin/phpbin php:lint
`

- PHP Metrics (Code metrics generator)
`
php vendor/bin/phpbin php:metrics
`

- PHP PSR1
`
php vendor/bin/phpbin php:psr1
`
- PHP PSR1-fix
`
php vendor/bin/phpbin php:psr1:fix
`
- PHP PSR2
`
php vendor/bin/phpbin php:psr2
`
- PHP PSR2-fix
`
php vendor/bin/phpbin php:psr2:fix
`
- PHP unit test (pass all tests in `/test` directory)
`
php vendor/bin/phpbin php:test
`
## Composer helpers:

Provides some commands to manage a composer more easily:

#### Composer install dev
- Composer install:
`
php vendor/bin/phpbin composer:install [package_name] [module_name] [envoirment]
`

This command, add a package into a module (subtree), this package will added into file composer.json (in `require` or `require-dev`, deppends envoirment parameter) of module, 
and will install into root project (`vendor` folder).

#### Composer get dev packages
- Composer dev packages:
`
php vendor/bin/phpbin composer:dev-packages [module_name]
`

Load all module requires and require-dev to root file composer.json like require-dev

#### Composer solve versions conflicts
- Composer update versions:
`
php vendor/bin/phpbin composer:update-versions [module_name]
`

Solve conflicts about versions from root project composer.json, to module or modules composer.json file.

#### Composer normalize
- Composer normalize composer.json files:
`
php vendor/bin/phpbin composer:normalize [module_name]
`

Normalize structure of project composer.json, and module or modules composer.json file.

## [Changelog](https://github.com/articstudio/php-bin/releases)

**2019-1-28 / [v1.0.2](https://github.com/articstudio/php-bin/releases/tag/1.0.2)**
- Added documentation

**2019-1-26 / [v1.0.1](https://github.com/articstudio/php-bin/releases/tag/1.0.1)**
- Add CircleCi integration
- PSR2 code fixes

**2019-1-25 / [v1.0.0](https://github.com/articstudio/php-bin/releases/tag/1.0.0)**
- Git Subtree
- Composer
- PHP