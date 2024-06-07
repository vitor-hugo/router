> [!NOTE]
> This package is under development.

# Router <!-- omit in toc -->

A simple PHP router system using PHP [Attributes](https://www.php.net/manual/en/language.attributes.php).

# Table of Contents <!-- omit in toc -->

- [Requirements](#requirements)
- [Installing](#installing)
- [Tests](#tests)
  - [With Makefile](#with-makefile)
  - [Manually](#manually)
- [Contribute](#contribute)
- [License](#license)

# Requirements

- PHP 8.2+
- Composer 2+ (Package manager)
- PHPUnit 11+ (Automated tests)

# Installing

```shell
composer require torugo/router
```

# Tests

## With Makefile

To run the tests with `make` use:

```
make test
```

or with PHPUnit testdox

```shell
make testdox
```

## Manually

> [!TIP]
> - You will need to use two terminal windows to run the tests manually.  
> - All commands must be executed from the project root.

### 1. Start Test Server <!-- omit in toc -->
In the first window terminal type:
```shell
php -S localhost\:8000 -t tests/ServerTests/Server/
```

### 2. Run the tests <!-- omit in toc -->
In the second window, from the project root type:
```shell
./vendor/bin/phpunit
```

# Contribute

It is currently not open to contributions, I intend to make it available as soon as possible.

# License

Router is licensed under the MIT License - see the LICENSE file for details.
