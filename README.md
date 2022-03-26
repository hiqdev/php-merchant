# PHP merchant library

**Generalization over Omnipay**

[![GitHub Actions](https://github.com/hiqdev/php-merchant/workflows/Tests/badge.svg)](https://github.com/hiqdev/php-merchant/actions)
[![Latest Stable Version](https://poser.pugx.org/hiqdev/php-merchant/v/stable)](https://packagist.org/packages/hiqdev/php-merchant)
[![Total Downloads](https://poser.pugx.org/hiqdev/php-merchant/downloads)](https://packagist.org/packages/hiqdev/php-merchant)

This package provides generalized interface for [Omnipay] and [Payum]:

- generalized `purse` and `secret` for gateways
- generalized `time` and `payer` for responses

[Omnipay] is a framework agnostic, multi-gateway payment processing library for PHP 7.1+.

[Omnipay]:  http://omnipay.thephpleague.com/
[Payum]:    http://payum.org/

## Installation

The preferred way to install this library is through [composer](http://getcomposer.org/download/).

Either run

```sh
php composer.phar require "hiqdev/php-merchant"
```

or add

```json
"hiqdev/php-merchant": "^2.0"
```

to the require section of your composer.json.

## License

This project is released under the terms of the BSD-3-Clause [license](LICENSE).
Read more [here](http://choosealicense.com/licenses/bsd-3-clause).

Copyright © 2015-2022, HiQDev (http://hiqdev.com/)
