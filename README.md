# Magento 2 module for New Relic

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/yireo/magento2-newrelic2)
<!-- badges.specs.end -->

## Current status

<!-- badges.test.start -->
![Static Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_NewRelic2/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_NewRelic2/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_NewRelic2/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_NewRelic2/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_NewRelic2/compile.yml?label=compile)
<!-- badges.test.end -->

## About
New Relic offers monitoring of your webapplications, by installing a daemon on the webserver and hooking into the PHP core. This allows monitoring of specific PHP functions. This Yireo extensions extends this
behaviour so that additional Magento information can be logged.

This is not an official New Relic product, and is not endorsed by New Relic. Note that some of the functionality is only available with the New Relic Pro version: Custom metrics are only available through custom dashboards, which can only be created in the New Relic Pro subscriptions. Additionally, our Magento extension provides additional variables, which are only available with transaction tracking which again requires the Pro-version.

## Installation
### Instructions for using composer
Use composer to install this extension. First make sure that Magento is installed via composer, and that there is a valid `composer.json` file present.

Next, install our module using the following command:

    composer require yireo/magento2-newrelic2

Next, install the new module into Magento itself:

    ./bin/magento module:enable Yireo_NewRelic2
    ./bin/magento setup:upgrade

Check whether the module is successfully installed in **Admin > Stores >
Configuration > Advanced > Advanced**.

Done.

### Instructions for manual copy
We recommend `composer` to install this package. However, if you want a manual copy instead, these are the steps:
* Make sure to install New Relic on your server first
* Make sure to have New Relics PHP module installed and active
* Upload the files in the `source/` folder to the folder `app/code/Yireo/NewRelic2` of your site
* Run `php -f bin/magento module:enable Yireo_NewRelic2`
* Run `php -f bin/magento setup:upgrade`
* Flush the Magento cache
* Configure settings under `Stores > Configuration > Advanced > Yireo New Relic`
* Done

## Usage
- Make sure to install the New Relic PHP Agent first. See the New Relic site for details on this.
- Make sure the New Relic PHP Agent is running.
- Make sure that you are receiving data in your New Relic monitoring.
- Install our extension for Magento.
- Configure the options in the Magento Configuration:
    - Set the **Application Name** to whatever you like.
    - Configure the **License Key** as received from New Relic.

## Technical overview
This extension implements an `Observer` to send various events to the New Relic dashboard. This extension implements two `Blocks` for browser timings via New Relic. One block for the header, one block for the body.
This extension intercepts the class `Magento\Framework\View\Element\AbstractBlock` to send a custom parameter per Magento block to New Relic.

## Testing
### Unit testing
This extension ships with PHPUnit tests. The generic PHPUnit configuration in Magento 2 will pick up on these tests. To only
test Yireo extensions, copy the file `phpunit.xml.yireo` to your Magento folder `dev/tests/unit`. Next, from within that folder run PHPUnit. For instance:

    phpunit -c phpunit.xml.yireo --bootstrap PATH_TO_MAGENTO/dev/tests/unit/framework/bootstrap.php

## PWA compatibility
This frontend extension will be completely replaced by React or Vue functionality, and therefore, this extension
will not be made compatible with PWA.
