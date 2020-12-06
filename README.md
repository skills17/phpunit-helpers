# skills17/phpunit-helpers

<img src="https://cyrilwanner.github.io/packages/skills17/phpunit-helpers/assets/output-preview.png" align="center">

This package provides some PHPUnit helpers for usage in a skills competition environment. It includes:
- Custom output formatter
- Base test classes
- Automatic database resets
- ... and more

## Table of contents

- [Installation](#installation)
- [Usage](#usage)
  - [Grouping](#grouping)
  - [Non-database tests](#non-database-tests)
  - [Database tests](#database-tests)
  - [Extra tests](#extra-tests)
- [Best practices](#best-practices)
  - [Time limit](#time-limit)
  - [Writing good test](#writing-good-test)
- [License](#license)

## Installation

**Requirements**:
- PHPUnit `9.0` or greater
- PHP `7.4` or greater

To install this package, simply run the following command:
```bash
composer require skills17/phpunit-helpers
```

Additionally, create a `phpunit.xml` file in the root folder of your task:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
         cacheResultFile="vendor/phpunit/phpunit.cache"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         processIsolation="false"
         stopOnFailure="false"
         printerClass="Skills17\PHPUnit\ResultPrinter"
         printerFile="vendor/skills17/phpunit-helpers/src/Printer.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Tests">
            <directory>./tests/</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

To use the provided result printer, the `printerClass` and `printerFile` settings are required.
The other ones are suggested settings but can be modified to match your requirements.

## Usage

A `config.json` file needs to be created that contains some information about the task.
It should be placed in the root folder of your task, next to the `composer.json` file.

### Grouping

A core concept is test groups. You usually don't want to test everything for one criterion in one
test function but instead split it into multiple ones for a cleaner test class and a better overview.

In PHP, tests are grouped by a function prefix defined in the `config.json` file.

For example, if you have the criteria that `GET /countries` is implemented correctly and that
awards 3 points, you could have the following test functions:
- `testCountriesIndexAll`: test if all available countries get returned
- `testCountriesIndexLimit`: test if an optional limit is respected
- `testCountriesIndexSearch`: test if countries can be searched by an optional query

Now, all these functions have a common prefix: `CountriesIndex` (`test` can be omitted) and the
`config.json` will look similar to this:
```json
{
  "groups": [
    {
      "match": "CountriesIndex.+"
    }
  ]
}
```

Each one of the test methods will now award 1 point if they pass which results in 3 points in total
for the whole group. If you want that _all_ three test functions have to pass to get 3 points
(and 0 otherwise), you can set the `"required": true` attribute.

### Non-database tests

To write a test for an application or a part of it that does not require a database, you can simply
extend the `Skills17\PHPUnit\BaseTest` class. It further extends the normal `TestCase` class of
PHPUnit so all PHPUnit assert functions are available as well.

### Database tests

If the application under test requires a database, it should get reset before every test run to make
sure the data is consistent across multiple test runs. For this case, there are two classes
available to extend.

Additionally, a database dump has to be provided and specified in the `config.json` if it does not
have the default name.

#### Read tests

If the test and the part of the application that gets tested only _reads_ data from the database but
never writes/changes it, the `Skills17\PHPUnit\Database\ReadTest` class should get extended.

To improve the performance, this type of test only resets the database once before the test class
gets executed.

#### Write tests

If the test or the part of the application that gets tested _writes_ data (insert, update, delete),
the `Skills17\PHPUnit\Database\WriteTest` class should get extended.

This makes sure that the database gets reset before every test function in the class gets executed,
not only once at the beginning of the class like in a read test.
It makes sure that each function starts with a fresh database dump, which is especially useful
when a previous function failed and so the database is in an unknown state or when only a subset
of functions get executed as they have been filtered.

### Extra tests

To prevent cheating, extra tests can be used. They are not available to the competitors and should
test exactly the same things as the normal tests do, but with different values.

For example, if your normal test contains a check to search the list of all countries by 'Sw*',
copy the test into an extra test and change the search string to 'Ca*'.
Since the competitors will not know the extra test, it would detect statically returned values
that were returned to simply satisfy the 'Sw*' tests instead of actually implement the search logic.

Extra tests are detected by their namespace, which should contain `\Extra\`. That means, if your
normal test is in the `Skills17\CountriesApp\Test` namespace, the extra test could be in
`Skills17\CountriesApp\Test\Extra`. The class- and method names should exactly equal the ones from
the normal tests. If they don't, a warning will be displayed.

If an extra test fails while the corresponding normal test passes, a warning will be displayed that
a manual review of that test is required since it detected possible cheating.
The penalty then has to be decided manually from case to case, the points visible in the output
assumed that the test passed and there was no cheating.

For the distribution of the task to the competitors, simply delete the folder containing all extra
tests. Nothing else needs to be done or configured.

## Best practices

### Time limit

It is strongly recommended to enforce a time limit on the tests. Otherwise, an endless loop can
break the whole testing pipeline.

The following steps show how a time limit can be configured.

1. Install the composer package `phpunit/php-invoker`
1. Add `enforceTimeLimit="true"` to the `phpunit.xml` config file
1. Annotate all tests with eather `@large` (60s timeout), `@medium` (10s timeout) or
`@small` (1s timeout)

The timeouts for all test sizes can be [configured](https://phpunit.readthedocs.io/en/9.3/risky-tests.html#risky-tests-test-execution-timeout).

### Writing good test

For additional advice for writing good tests in a competition environment, read [this blog post](https://skills17.ch/blog/automated-testing-in-a-competition-environment-2020).

## License

[MIT](https://github.com/skills17/phpunit-helpers/blob/master/LICENSE)
