# PHP/SAP implementation for Kouckys legacy saprfc module

[![License: MIT][license-mit]](LICENSE)
[![Build Status][travis-badge]][travis-ci]
[![Maintainability][maintainability-badge]][maintainability]
[![Test Coverage][coverage-badge]][coverage]

This repository implements the [PHP/SAP][phpsap] interface for [Eduard Kouckys legacy saprfc PHP module][koucky].

## Usage

```sh
composer require php-sap/saprfc-koucky:^2.0
```

```php
<?php
use phpsap\saprfc\SapRfcConfigA;
use phpsap\saprfc\SapRfcConnection;

$result = (new SapRfcConnection(new SapRfcConfigA([
  'ashost' => 'sap.example.com',
  'sysnr' => '001',
  'client' => '002',
  'user' => 'username',
  'passwd' => 'password'
])))
    ->prepareFunction('MY_COOL_SAP_REMOTE_FUNCTION')
    ->setParam('INPUT_PARAM', 'some input value')
    ->invoke();
```

For further documentation, please read the documentation on [PHP/SAP][phpsap]!

[phpsap]: https://php-sap.github.io
[koucky]: http://saprfc.sourceforge.net/ "SAPRFC extension module for PHP"
[license-mit]: https://img.shields.io/badge/license-MIT-blue.svg
[travis-badge]: https://travis-ci.org/php-sap/saprfc-koucky.svg?branch=master
[travis-ci]: https://travis-ci.org/php-sap/saprfc-koucky
[maintainability-badge]: https://api.codeclimate.com/v1/badges/1c67c34d571c4a0a1492/maintainability
[maintainability]: https://codeclimate.com/github/php-sap/saprfc-koucky/maintainability
[coverage-badge]: https://api.codeclimate.com/v1/badges/1c67c34d571c4a0a1492/test_coverage
[coverage]: https://codeclimate.com/github/php-sap/saprfc-koucky/test_coverage
