hiqdev/php-merchant
-------------------

## [1.0.0] - 2018-07-27

- Added ePayments support
    - [74552da] 2018-07-23 Added ePayments [@SilverFire]
    - [70a617a] 2018-07-26 Pass TransactionReference as Payer for Epayments [@SilverFire]
- Added 2checkout support
    - [525b5a4] 2017-12-01 Inited TwoCheckoutPlusMerchant [@SilverFire]
    - [e1aeba4] 2017-12-01 Added collizo4sky/omnipay-2checkout [@SilverFire]
- Added Robokassa support
    - [a360367] 2017-10-24 Fixed Robokassa tests [@SilverFire]
    - [c46f53a] 2017-10-24 Updated RoboKassaMerchant to extract TransactionReference [@SilverFire]
    - [a5e274d] 2017-10-24 Implemented second key support for Robokassa [@SilverFire]
    - [ece05ec] 2017-10-23 Added Robokassa support [@SilverFire]
- Completely rewritten to a better architecture style
    - [148578a] 2017-10-17 Fixed tests [@SilverFire]
    - [4dbce47] 2018-07-23 Updated to latest PHPUnit, fixed broken tests [@SilverFire]
    - [eafb027] 2018-05-30 Fixed PHPDocs [@SilverFire]
    - [e844c6b] 2018-03-05 csfixed [@hiqsol]
    - [91b6be4] 2017-10-17 Changed signAlgorythm [@SilverFire]
    - [2606466] 2017-10-13 Docs [@SilverFire]
    - [80f70d8] 2017-10-11 Refactored to use AbstractMerchant, added more tests [@SilverFire]
    - [346224f] 2017-10-10 Tests, deep coding [@SilverFire]
    - [3153201] 2017-10-09 Working on tests [@SilverFire]
    - [cb2b772] 2017-10-09 Continue refactoring [@SilverFire]
    - [57c7f3b] 2017-10-09 Dropped old implementation [@SilverFire]
    - [30280a7] 2017-10-09 Refactored to spread MoneyPHP/Money VO [@SilverFire]
    - [9d67897] 2017-10-06 Deep coding [@SilverFire]
    - [365c6e2] 2017-10-04 Deep coding [@SilverFire]
    - [a43b074] 2017-11-26 quickfixed epayservice test: changed check_key [@hiqsol]
    - [a5e9c0a] 2017-11-25 fixing build [@hiqsol]
    - [1639588] 2017-11-25 added dependencies: phpmoney, omnipay freekassa, robokassa, yandexmoney, webmoney [@hiqsol]
    - [e4d1f34] 2017-11-25 csfixed [@hiqsol]
    - [ad21c4c] 2017-10-24 Removed redundant `use` operators [@SilverFire]
- Added BitPay support
    - [e17acc9] 2017-10-17 BitPay request should return response with GET method [@SilverFire]
    - [189007b] 2017-10-03 Updated to use hiqdev/omnipay-bitpay [@SilverFire]
    - [206a1d1] 2017-08-05 csfixed [@hiqsol]
    - [f4cae51] 2017-08-05 renamed `hidev.yml` [@hiqsol]
    - [21f1529] 2017-08-02 Implemented bitpay support [@SilverFire]
    - [abec28c] 2018-01-17 Added corner case handling when BitPay fails to create invoice [@SilverFire]
- Added YandexMoney support
    - [a032e95] 2017-03-30 Updated yandexmoney proxy to follow API changes [@SilverFire]
    - [247cd70] 2017-03-30 added formatting money (sum, amount) for changes in omnipay [@hiqsol]
    - [184d52a] 2017-03-30 Yandexmoney proxy switched to Omnipay\YandexMoney\P2pGateway [@SilverFire]
    - [5c8cb6b] 2017-03-28 Added yandexmoney adapter [@SilverFire]
- Added FreeKassa support
    - [259fd89] 2017-10-25 Added FreeKassa support [@SilverFire]
    - [dc79b93] 2017-03-29 added **FreeKassa** to OmnipayMerchant [@hiqsol]
    - [92357d1] 2017-03-28 Added AbstractMerchant::label, AbstractMerchant::getGatewayNamespacePart() [@SilverFire]
    - [3b57527] 2017-01-20 Updated CHNAGELOG [@SilverFire]
- Added `commission_fee` related things to the AbstractRequest
    - [5c419c8] 2017-01-20 csfixed [@SilverFire]
    - [abc99fe] 2017-01-20 Added `commission_fee` to the AbstractRequest [@SilverFire]
- Other minor changes
    - [4b9050e] 2018-01-15 Fixed Paxum gateway URL [@BladeRoot]
    - [d42c545] 2017-12-13 Fixed WebmoneyMerchant to convert time to UTC [@SilverFire]

## [0.0.3] - 2016-09-16

- Fixed errored requirements
    - [2a29191] 2016-09-16 fixed errored requirements [@hiqsol]

## [0.0.2] - 2016-09-15

- Fixed minor issues
    - [7069672] 2016-09-15 redone bumping with `chkipper` [@hiqsol]
    - [2133c9c] 2016-09-15 skipped test [@hiqsol]
    - [6ae3236] 2016-09-15 rehideved [@hiqsol]
    - [f615990] 2016-03-25 + proper defaulting for getSum/Fee/Time [@hiqsol]
    - [c2307aa] 2016-03-23 try to fix [@SilverFire]
- Added WebmoneyMerchant class
    - [6231d19] 2016-03-23 Added WebmoneyMerchant class [@SilverFire]
    - [efa7eae] 2016-01-25 added more tests [@hiqsol]
    - [854744c] 2016-01-24 added more tests [@hiqsol]
    - [8448ee6] 2016-01-18 rehideved [@hiqsol]
    - [acbca3c] 2016-01-18 phpcsfixed [@hiqsol]
    - [6da1b24] 2016-01-11 temporary added paypal4genuine [@hiqsol]
    - [803b68e] 2015-12-24 fixed build [@hiqsol]
    - [56504e6] 2015-12-17 fixed build [@hiqsol]
    - [48299aa] 2015-12-16 small improvements around response [@hiqsol]

## [0.0.1] - 2015-12-15

- Added tests
    - [04120b9] 2015-12-15 php-cs-fixed [@hiqsol]
    - [30d38b1] 2015-12-15 + tests [@hiqsol]
    - [2c8733f] 2015-12-15 fixed travis [@hiqsol]
- Added exception classes and phpdocs
    - [069d612] 2015-12-14 PHPDocs updated [@SilverFire]
    - [3e10511] 2015-12-11 PHPDocs improved [@SilverFire]
    - [02c352d] 2015-12-11 Added exception classes [@SilverFire]
- Added gateway specifics facility
    - [da622fc] 2015-12-10 + added gateway specific ability, and WebMoney specific merchant and response [@hiqsol]
- Changed: redone to be generalization over Omnipay and Payum
    - [d9ea017] 2015-12-10 fixed AbstractResponse: - yii dependency [@hiqsol]
    - [c664dcc] 2015-12-09 fixed merchant and gateway creation [@hiqsol]
    - [a7a39b5] 2015-12-08 redoning with MerchantManager [@hiqsol]
    - [7bb6e51] 2015-12-07 redoing to omnipay/payum [@hiqsol]
    - [ce7f761] 2015-11-04 NOT FINISHED redoing to omnipay [@hiqsol]
- Added basics
    - [f86effd] 2015-11-04 php-cs-fixed [@hiqsol]
    - [889dabe] 2015-11-04 + getConfig() [@hiqsol]
    - [13c6a7f] 2015-11-04 redone description/label -> paymentDescription/Label [@hiqsol]
    - [58fedba] 2015-11-04 removed home-made exceptions [@hiqsol]
    - [746e713] 2015-10-31 changed returnUrl to get parameters; + mget() [@hiqsol]
    - [acc77aa] 2015-10-30 redone validateMoney <- checkMoney [@hiqsol]
    - [d8de4f9] 2015-10-30 renamed 'scheme' <- 'proto' [@hiqsol]
    - [7e12a55] 2015-10-30 changed: redone to use system <- name [@hiqsol]
    - [ed71fa4] 2015-10-23 + getCents/Time/UniqId, + formatDatetime, + checkMoney, + curl [@hiqsol]
    - [e7330e2] 2015-10-22 removed PayPalMerchant to hiqdev\php-merchant-paypal [@hiqsol]
    - [d3cdf7a] 2015-10-22 + rendering form, + guessClass [@hiqsol]
    - [e4440a8] 2015-10-21 php-cs-fixed [@hiqsol]
    - [94c692a] 2015-10-21 hideved [@hiqsol]
    - [ae6db66] 2015-10-21 inited [@hiqsol]

## [Development started] - 2015-10-21

[@hiqsol]: https://github.com/hiqsol
[sol@hiqdev.com]: https://github.com/hiqsol
[@SilverFire]: https://github.com/SilverFire
[d.naumenko.a@gmail.com]: https://github.com/SilverFire
[@tafid]: https://github.com/tafid
[andreyklochok@gmail.com]: https://github.com/tafid
[@BladeRoot]: https://github.com/BladeRoot
[bladeroot@gmail.com]: https://github.com/BladeRoot
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
[7069672]: https://github.com/hiqdev/php-merchant/commit/7069672
[2a29191]: https://github.com/hiqdev/php-merchant/commit/2a29191
[5c419c8]: https://github.com/hiqdev/php-merchant/commit/5c419c8
[abc99fe]: https://github.com/hiqdev/php-merchant/commit/abc99fe
[Under development]: https://github.com/hiqdev/php-merchant/compare/0.0.3...HEAD
[0.0.3]: https://github.com/hiqdev/php-merchant/compare/0.0.2...0.0.3
[0.0.2]: https://github.com/hiqdev/php-merchant/compare/0.0.1...0.0.2
[0.0.1]: https://github.com/hiqdev/php-merchant/releases/tag/0.0.1
[74552da]: https://github.com/hiqdev/php-merchant/commit/74552da
[4dbce47]: https://github.com/hiqdev/php-merchant/commit/4dbce47
[eafb027]: https://github.com/hiqdev/php-merchant/commit/eafb027
[e844c6b]: https://github.com/hiqdev/php-merchant/commit/e844c6b
[abec28c]: https://github.com/hiqdev/php-merchant/commit/abec28c
[4b9050e]: https://github.com/hiqdev/php-merchant/commit/4b9050e
[d42c545]: https://github.com/hiqdev/php-merchant/commit/d42c545
[525b5a4]: https://github.com/hiqdev/php-merchant/commit/525b5a4
[e1aeba4]: https://github.com/hiqdev/php-merchant/commit/e1aeba4
[a43b074]: https://github.com/hiqdev/php-merchant/commit/a43b074
[a5e9c0a]: https://github.com/hiqdev/php-merchant/commit/a5e9c0a
[1639588]: https://github.com/hiqdev/php-merchant/commit/1639588
[e4d1f34]: https://github.com/hiqdev/php-merchant/commit/e4d1f34
[259fd89]: https://github.com/hiqdev/php-merchant/commit/259fd89
[ad21c4c]: https://github.com/hiqdev/php-merchant/commit/ad21c4c
[a360367]: https://github.com/hiqdev/php-merchant/commit/a360367
[c46f53a]: https://github.com/hiqdev/php-merchant/commit/c46f53a
[a5e274d]: https://github.com/hiqdev/php-merchant/commit/a5e274d
[ece05ec]: https://github.com/hiqdev/php-merchant/commit/ece05ec
[148578a]: https://github.com/hiqdev/php-merchant/commit/148578a
[e17acc9]: https://github.com/hiqdev/php-merchant/commit/e17acc9
[91b6be4]: https://github.com/hiqdev/php-merchant/commit/91b6be4
[2606466]: https://github.com/hiqdev/php-merchant/commit/2606466
[80f70d8]: https://github.com/hiqdev/php-merchant/commit/80f70d8
[346224f]: https://github.com/hiqdev/php-merchant/commit/346224f
[3153201]: https://github.com/hiqdev/php-merchant/commit/3153201
[cb2b772]: https://github.com/hiqdev/php-merchant/commit/cb2b772
[57c7f3b]: https://github.com/hiqdev/php-merchant/commit/57c7f3b
[30280a7]: https://github.com/hiqdev/php-merchant/commit/30280a7
[9d67897]: https://github.com/hiqdev/php-merchant/commit/9d67897
[365c6e2]: https://github.com/hiqdev/php-merchant/commit/365c6e2
[189007b]: https://github.com/hiqdev/php-merchant/commit/189007b
[206a1d1]: https://github.com/hiqdev/php-merchant/commit/206a1d1
[f4cae51]: https://github.com/hiqdev/php-merchant/commit/f4cae51
[21f1529]: https://github.com/hiqdev/php-merchant/commit/21f1529
[a032e95]: https://github.com/hiqdev/php-merchant/commit/a032e95
[247cd70]: https://github.com/hiqdev/php-merchant/commit/247cd70
[184d52a]: https://github.com/hiqdev/php-merchant/commit/184d52a
[dc79b93]: https://github.com/hiqdev/php-merchant/commit/dc79b93
[92357d1]: https://github.com/hiqdev/php-merchant/commit/92357d1
[5c8cb6b]: https://github.com/hiqdev/php-merchant/commit/5c8cb6b
[3b57527]: https://github.com/hiqdev/php-merchant/commit/3b57527
[70a617a]: https://github.com/hiqdev/php-merchant/commit/70a617a
[1.0.0]: https://github.com/hiqdev/php-merchant/compare/0.0.3...1.0.0
