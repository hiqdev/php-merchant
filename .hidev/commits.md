hiqdev/php-merchant commits history
-----------------------------------

## Under development

- Fixed minor issues
    - 56504e6 2015-12-17 fixed build (sol@hiqdev.com)
    - 48299aa 2015-12-16 small improvements around response (sol@hiqdev.com)

## 0.0.1 2015-12-15

- Added tests
    - 04120b9 2015-12-15 php-cs-fixed (sol@hiqdev.com)
    - 30d38b1 2015-12-15 + tests (sol@hiqdev.com)
    - 2c8733f 2015-12-15 fixed travis (sol@hiqdev.com)
- Added exception classes and phpdocs
    - 069d612 2015-12-14 PHPDocs updated (d.naumenko.a@gmail.com)
    - 3e10511 2015-12-11 PHPDocs improved (d.naumenko.a@gmail.com)
    - 02c352d 2015-12-11 Added exception classes (d.naumenko.a@gmail.com)
- Added gateway specifics facility
    - da622fc 2015-12-10 + added gateway specific ability, and WebMoney specific merchant and response (sol@hiqdev.com)
- Changed: redone to be generalization over Omnipay and Payum
    - d9ea017 2015-12-10 fixed AbstractResponse: - yii dependency (sol@hiqdev.com)
    - c664dcc 2015-12-09 fixed merchant and gateway creation (sol@hiqdev.com)
    - a7a39b5 2015-12-08 redoning with MerchantManager (sol@hiqdev.com)
    - 7bb6e51 2015-12-07 redoing to omnipay/payum (sol@hiqdev.com)
    - ce7f761 2015-11-04 NOT FINISHED redoing to omnipay (sol@hiqdev.com)
- Added basics
    - f86effd 2015-11-04 php-cs-fixed (sol@hiqdev.com)
    - 889dabe 2015-11-04 + getConfig() (sol@hiqdev.com)
    - 13c6a7f 2015-11-04 redone description/label -> paymentDescription/Label (sol@hiqdev.com)
    - 58fedba 2015-11-04 removed home-made exceptions (sol@hiqdev.com)
    - 746e713 2015-10-31 changed returnUrl to get parameters; + mget() (sol@hiqdev.com)
    - acc77aa 2015-10-30 redone validateMoney <- checkMoney (sol@hiqdev.com)
    - d8de4f9 2015-10-30 renamed 'scheme' <- 'proto' (sol@hiqdev.com)
    - 7e12a55 2015-10-30 changed: redone to use system <- name (sol@hiqdev.com)
    - ed71fa4 2015-10-23 + getCents/Time/UniqId, + formatDatetime, + checkMoney, + curl (sol@hiqdev.com)
    - e7330e2 2015-10-22 removed PayPalMerchant to hiqdev\php-merchant-paypal (sol@hiqdev.com)
    - d3cdf7a 2015-10-22 + rendering form, + guessClass (sol@hiqdev.com)
    - e4440a8 2015-10-21 php-cs-fixed (sol@hiqdev.com)
    - 94c692a 2015-10-21 hideved (sol@hiqdev.com)
    - ae6db66 2015-10-21 inited (sol@hiqdev.com)

## Development started 2015-10-21

