# PHP merchant library

**Generalization over Omnipay and Payum**

[![Latest Stable Version](https://poser.pugx.org/hiqdev/php-merchant/v/stable)](https://packagist.org/packages/hiqdev/php-merchant)
[![Total Downloads](https://poser.pugx.org/hiqdev/php-merchant/downloads)](https://packagist.org/packages/hiqdev/php-merchant)
[![Build Status](https://img.shields.io/travis/hiqdev/php-merchant.svg)](https://travis-ci.org/hiqdev/php-merchant)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/hiqdev/php-merchant.svg)](https://scrutinizer-ci.com/g/hiqdev/php-merchant/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/hiqdev/php-merchant.svg)](https://scrutinizer-ci.com/g/hiqdev/php-merchant/)

This package provides generalized interface for [Omnipay] and [Payum]:

- generalized `purse` and `secret` for gateways
- generalized `time` and `payer` for responses
- else?

[Omnipay] is a framework agnostic, multi-gateway payment processing library for PHP 5.3+.

[Payum] is payment processing framework for PHP 5.5+.

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
"hiqdev/php-merchant": "*"
```

to the require section of your composer.json.

## License

This project is released under the terms of the BSD-3-Clause [license](LICENSE).
Read more [here](http://choosealicense.com/licenses/bsd-3-clause).

Copyright © 2015-2017, HiQDev (http://hiqdev.com/)
