>  
> !!! This package is under development !!!
>   

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

```shell
make test
```

or with PHPUnit testdox

```shell
make testdox
```

## Manually

### 1. Start Test Server <!-- omit in toc -->
```shell
php -S localhost\:8000 -t tests/ServerTests/Server/
```

### 2. Run the test <!-- omit in toc -->

From the project root, type:
> You will probably need to use another terminal window.
```shell
./vendor/bin/phpunit
```
# Contribute

It is currently not open to contributions, I intend to make it available as soon as possible.

# License

Router is licensed under the MIT License - see the LICENSE file for details.
