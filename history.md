hiqdev/php-merchant
-------------------

## [Under development]

- Added WebmoneyMerchant class
    - [2133c9c] 2016-09-15 skipped test [sol@hiqdev.com]
    - [6ae3236] 2016-09-15 rehideved [sol@hiqdev.com]
    - [f615990] 2016-03-25 + proper defaulting for getSum/Fee/Time [sol@hiqdev.com]
    - [c2307aa] 2016-03-23 try to fix [d.naumenko.a@gmail.com]
    - [6231d19] 2016-03-23 Added WebmoneyMerchant class [d.naumenko.a@gmail.com]
    - [efa7eae] 2016-01-25 added more tests [sol@hiqdev.com]
    - [854744c] 2016-01-24 added more tests [sol@hiqdev.com]
    - [8448ee6] 2016-01-18 rehideved [sol@hiqdev.com]
    - [acbca3c] 2016-01-18 phpcsfixed [sol@hiqdev.com]
    - [6da1b24] 2016-01-11 temporary added paypal4genuine [sol@hiqdev.com]
    - [803b68e] 2015-12-24 fixed build [sol@hiqdev.com]
    - [56504e6] 2015-12-17 fixed build [sol@hiqdev.com]
    - [48299aa] 2015-12-16 small improvements around response [sol@hiqdev.com]

## [0.0.1] - 2015-12-15

- Added tests
    - [04120b9] 2015-12-15 php-cs-fixed [sol@hiqdev.com]
    - [30d38b1] 2015-12-15 + tests [sol@hiqdev.com]
    - [2c8733f] 2015-12-15 fixed travis [sol@hiqdev.com]
- Added exception classes and phpdocs
    - [069d612] 2015-12-14 PHPDocs updated [d.naumenko.a@gmail.com]
    - [3e10511] 2015-12-11 PHPDocs improved [d.naumenko.a@gmail.com]
    - [02c352d] 2015-12-11 Added exception classes [d.naumenko.a@gmail.com]
- Added gateway specifics facility
    - [da622fc] 2015-12-10 + added gateway specific ability, and WebMoney specific merchant and response [sol@hiqdev.com]
- Changed: redone to be generalization over Omnipay and Payum
    - [d9ea017] 2015-12-10 fixed AbstractResponse: - yii dependency [sol@hiqdev.com]
    - [c664dcc] 2015-12-09 fixed merchant and gateway creation [sol@hiqdev.com]
    - [a7a39b5] 2015-12-08 redoning with MerchantManager [sol@hiqdev.com]
    - [7bb6e51] 2015-12-07 redoing to omnipay/payum [sol@hiqdev.com]
    - [ce7f761] 2015-11-04 NOT FINISHED redoing to omnipay [sol@hiqdev.com]
- Added basics
    - [f86effd] 2015-11-04 php-cs-fixed [sol@hiqdev.com]
    - [889dabe] 2015-11-04 + getConfig() [sol@hiqdev.com]
    - [13c6a7f] 2015-11-04 redone description/label -> paymentDescription/Label [sol@hiqdev.com]
    - [58fedba] 2015-11-04 removed home-made exceptions [sol@hiqdev.com]
    - [746e713] 2015-10-31 changed returnUrl to get parameters; + mget() [sol@hiqdev.com]
    - [acc77aa] 2015-10-30 redone validateMoney <- checkMoney [sol@hiqdev.com]
    - [d8de4f9] 2015-10-30 renamed 'scheme' <- 'proto' [sol@hiqdev.com]
    - [7e12a55] 2015-10-30 changed: redone to use system <- name [sol@hiqdev.com]
    - [ed71fa4] 2015-10-23 + getCents/Time/UniqId, + formatDatetime, + checkMoney, + curl [sol@hiqdev.com]
    - [e7330e2] 2015-10-22 removed PayPalMerchant to hiqdev\php-merchant-paypal [sol@hiqdev.com]
    - [d3cdf7a] 2015-10-22 + rendering form, + guessClass [sol@hiqdev.com]
    - [e4440a8] 2015-10-21 php-cs-fixed [sol@hiqdev.com]
    - [94c692a] 2015-10-21 hideved [sol@hiqdev.com]
    - [ae6db66] 2015-10-21 inited [sol@hiqdev.com]

## [Development started] - 2015-10-21

[efa7eae]: https://github.com/hiqdev/php-merchant/commit/efa7eae
[854744c]: https://github.com/hiqdev/php-merchant/commit/854744c
[8448ee6]: https://github.com/hiqdev/php-merchant/commit/8448ee6
[acbca3c]: https://github.com/hiqdev/php-merchant/commit/acbca3c
[6da1b24]: https://github.com/hiqdev/php-merchant/commit/6da1b24
[803b68e]: https://github.com/hiqdev/php-merchant/commit/803b68e
[56504e6]: https://github.com/hiqdev/php-merchant/commit/56504e6
[48299aa]: https://github.com/hiqdev/php-merchant/commit/48299aa
[04120b9]: https://github.com/hiqdev/php-merchant/commit/04120b9
[30d38b1]: https://github.com/hiqdev/php-merchant/commit/30d38b1
[2c8733f]: https://github.com/hiqdev/php-merchant/commit/2c8733f
[069d612]: https://github.com/hiqdev/php-merchant/commit/069d612
[3e10511]: https://github.com/hiqdev/php-merchant/commit/3e10511
[02c352d]: https://github.com/hiqdev/php-merchant/commit/02c352d
[da622fc]: https://github.com/hiqdev/php-merchant/commit/da622fc
[d9ea017]: https://github.com/hiqdev/php-merchant/commit/d9ea017
[c664dcc]: https://github.com/hiqdev/php-merchant/commit/c664dcc
[a7a39b5]: https://github.com/hiqdev/php-merchant/commit/a7a39b5
[7bb6e51]: https://github.com/hiqdev/php-merchant/commit/7bb6e51
[ce7f761]: https://github.com/hiqdev/php-merchant/commit/ce7f761
[f86effd]: https://github.com/hiqdev/php-merchant/commit/f86effd
[889dabe]: https://github.com/hiqdev/php-merchant/commit/889dabe
[13c6a7f]: https://github.com/hiqdev/php-merchant/commit/13c6a7f
[58fedba]: https://github.com/hiqdev/php-merchant/commit/58fedba
[746e713]: https://github.com/hiqdev/php-merchant/commit/746e713
[acc77aa]: https://github.com/hiqdev/php-merchant/commit/acc77aa
[d8de4f9]: https://github.com/hiqdev/php-merchant/commit/d8de4f9
[7e12a55]: https://github.com/hiqdev/php-merchant/commit/7e12a55
[ed71fa4]: https://github.com/hiqdev/php-merchant/commit/ed71fa4
[e7330e2]: https://github.com/hiqdev/php-merchant/commit/e7330e2
[d3cdf7a]: https://github.com/hiqdev/php-merchant/commit/d3cdf7a
[e4440a8]: https://github.com/hiqdev/php-merchant/commit/e4440a8
[94c692a]: https://github.com/hiqdev/php-merchant/commit/94c692a
[ae6db66]: https://github.com/hiqdev/php-merchant/commit/ae6db66
[2133c9c]: https://github.com/hiqdev/php-merchant/commit/2133c9c
[6ae3236]: https://github.com/hiqdev/php-merchant/commit/6ae3236
[f615990]: https://github.com/hiqdev/php-merchant/commit/f615990
[c2307aa]: https://github.com/hiqdev/php-merchant/commit/c2307aa
[6231d19]: https://github.com/hiqdev/php-merchant/commit/6231d19
