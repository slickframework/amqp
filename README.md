# Slick AMQP Module

[![Latest Version](https://img.shields.io/github/release/slickframework/amqp.svg?style=flat-square)](https://github.com/slickframework/amqp/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/github/actions/workflow/status/slickframework/amqp/continuous-integration.yml?style=flat-square)](https://github.com/slickframework/amqp/actions/workflows/continuous-integration.yml)
[![Quality Score](https://img.shields.io/scrutinizer/g/slickframework/amqp/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/slickframework/amqp?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/slick/amqp.svg?style=flat-square)](https://packagist.org/packages/slick/amqp)

The Slick AMQP module provides an easy-to-use integration with the Advanced Message Queuing
Protocol (AMQP), enabling seamless communication between producers and consumers in PHP
applications. Designed to work with message brokers like RabbitMQ, this module supports
various exchange types, including **fanout**, **direct**, **headers**, and **topic**,
offering robust solutions for a wide range of messaging patterns.

This module is highly versatile and can be used in **any PHP project**, making it a great
choice for developers seeking advanced messaging capabilities. Additionally, it includes
special features to simplify integration, enablement, and configuration within the
Slick framework environment, offering an enhanced developer experience for Slick-based applications.

Whether you are building scalable microservices, managing asynchronous workflows, or
implementing event-driven architectures, the Slick AMQP module ensures reliable and efficient
message delivery across your system.

This package is compliant with PSR-2 code standards and PSR-4 autoload standards.
It also applies the semantic version 2.0.0 specification.

## Install

Via Composer

``` bash
$ composer require slick/amqp
```

## Usage
Please read `slick/amqp` documentation at [https://www.slick-framework.com/modules/amqp.html](https://www.slick-framework.com/modules/amqp.html)

## Testing

``` bash
$ vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email silvam.filipe@gmail.com instead of using the issue tracker.

## Credits

- [Slick framework](https://github.com/slickframework)
- [All Contributors](https://github.com/slickframework/amqp/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.