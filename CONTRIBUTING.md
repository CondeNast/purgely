# Contributing

Pull requests are gladly accepted. To ease the pull request process,
please adhere to these contributing guidelines. By participating in
this project, you agree to abide by its [code of conduct].

The project maintainers are very motivated to help anyone who has an
interest in contributing to this project. If any of the requirements
below are intimidating, please reach out to one of the maintainers.
Time permitting, she/he will be happy assist. We are committed to not
allowing your inexperience with these technologies be barrier to you
lending your perspective and skills to the project. Seriously, we are
here to help you help us.

## Development setup and submitting a pull request

1. Fork the repo to your Github account.
1. Clone your fork

    ```
    git clone git@github.com:your-username/purgely.git
    ```

1. If you don't have [Composer] installed, please [install Composer]
1. Install all project dependencies, including dev dependencies

    ```
    composer install
    ```

1. Write code that passes code style tests, along with unit tests
(details below).
1. Submit the pull request to the main project repo and ensure that all
checks are passing. Feel free to submit the pull request prior to
having all checks passing, but just know that it will not be merged
until the checks are passing.

All code is merged directly to master when it is ready for a merge.
Individual releases will be tagged as necessary. When working on a
patch branch off of master.

## Pull requests

Maintainers of this project aim for a high level of test covered code.
As such, pull requests will not be merged without tests; however, we
encourage participants to submit patches even before tests are
complete and are committed to helping motivated participants with test
coverage whether the maintainers write or they can teach participants
to write the tests.

All build tests must pass before a pull request will be merged.
Currently, the build process checks for:

* Passing tests
* Code style

## Testing

The test runner used for this project is [PHPUnit]. This project aims
to produce highly testable code with the primary goal of utilizing unit
tests to ensure stability, reliability, and quality.

Instead of the typical WordPress [plugin testing scaffold], this
project aims to test components of the code in isolation from
WordPress. This goal allows the code to be more easily testable given
that it is not dependent upon the WordPress environment. Additionally,
it makes it easier for participants to run unit tests in a reliable
and reproducible manner.

To achieve this aim, the test suite uses best testing practices (most
[prominently championed] by [Chris Hjartes]), along with the [WP_Mock]
for those unfortunate situations where the code cannot be completely
decoupled from WordPress.

To run the tests, use the following command:

```
composer test
```

These tests only assume you have run `composer install` and are working
in an environment with PHP installed.

Please note that the tests are executed in the build environment
against the following PHP versions:

* 5.3
* 5.4
* 5.5
* 5.6
* 7.0
* HHVM

Tests must pass in each environment before a pull request can be
accepted.

## Code style

This project proudly uses the WordPress Coding Style, which is enforced
by [PHP Code Sniffer] and the [WordPress Coding Standards sniffs].
There are some small deviations from this standard, which include:

* List items here...

To review your code for adherence to the standard, you can use the
following command:

```
composer style
```

PHP Code Sniffer tends to be very explicit in its reporting of
violations, which make it relatively easy to address violations;
however, should you experience any issues getting the code style checks
to pass, please ask the maintainers for assistance.

## Commit messages

While there are no explicit checks for "good commit messages", the
maintainers strive to keep a clean history of commits with great
messages that clearly *indicate intent for a change*. A good commit
message clearly explains why the change was necessary, not merely that
there was a change. It should be a helpful explanation of why a
decision was made to allow for more useful debugging as the project
grows.

It is not required, but heavily encouraged, that commit messages
include a subject and a body, with a blank line separating the two. The
subject should be no more than 50 characters and the body should not
span more than 72 columns per row. These standards allow for easier
readability across all platforms.

Good commit messages that clearly explain a *problem and a solution*
make reviewing work much easier and more likely to be merged.

For more information on great commit messages, please see [some
person's excellent article].

## Minimum PHP version

This project aims to be compatible with PHP 5.3, as 5.2 just shouldn't
be used anymore. Note that this minimum requirement could change in the
future.

## Acknowledgement

The [Thoughbot's] [contributing.md]  for the [Factory Girl Rails]
project was a source of inspiration for this document. Thank you!

[code of conduct]: https://github.com/CondeNast/purgely/blob/master/CONDUCT.md
[Composer]: https://getcomposer.org/
[install Composer]: https://getcomposer.org/doc/00-intro.md
[PHPUnit]: https://phpunit.de/
[plugin testing scaffold]: https://github.com/wp-cli/wp-cli/blob/v0.20.1/php/commands/scaffold.php#L584-L642
[prominently championed]: https://leanpub.com/grumpy-testing
[Chris Hjartes]: http://www.littlehart.net/atthekeyboard/
[WP_Mock]: https://github.com/10up/wp_mock
[WP CLI]: http://wp-cli.org/
[PHP_CodeSniffer]: https://github.com/squizlabs/PHP_CodeSniffer
[WordPress Coding Standards sniffs]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
[Chris Beams' excellent article]: http://chris.beams.io/posts/git-commit/
[Thoughtbot's]: https://thoughtbot.com/
[Factory Girl Rails]: https://github.com/thoughtbot/factory_girl_rails
[contributing.md]: https://github.com/thoughtbot/factory_girl_rails/blob/master/CONTRIBUTING.md