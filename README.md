# Magento 2 module for New Relic
================================
Homepage: http://www.yireo.com/software/magento-extensions/newrelic

Requirements:
* Magento 2 Merchant Beta or higher

Steps to get it working:
* Make sure to install New Relic on your server first
* Make sure to have New Relics PHP-module installed and active
* Upload the files in the source/ folder to your site
* Flush the Magento cache
* Configure settings under System > Configuration > Advanced > New Relic
* Done

## Unit testing
This extension ships with PHPUnit tests. The generic PHPUnit configuration in Magento 2 will pick up on these tests. To only
test Yireo extensions, copy the file `phpunit.xml.yireo` to your Magento folder `dev/tests/unit`. Next, from within that folder run PHPUnit. For instance:

    phpunit -c phpunit.xml.yireo
