# Magento 2 module for New Relic
================================
Homepage: http://www.yireo.com/software/magento-extensions/newrelic2

Requirements:
* Magento 2.0.0 Stable

We recommend `composer` to install this package. However, if you want a manual copy instead, these are the steps:
* Make sure to install New Relic on your server first
* Make sure to have New Relics PHP module installed and active
* Upload the files in the `source/` folder to the folder `app/code/Yireo/NewRelic2` of your site
* Flush the Magento cache
* Configure settings under `Stores > Configuration > Advanced > Yireo New Relic`
* Done


## Unit testing
This extension ships with PHPUnit tests. The generic PHPUnit configuration in Magento 2 will pick up on these tests. To only
test Yireo extensions, copy the file `phpunit.xml.yireo` to your Magento folder `dev/tests/unit`. Next, from within that folder run PHPUnit. For instance:

    phpunit -c phpunit.xml.yireo
