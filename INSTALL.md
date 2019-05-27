# Installation
## Instructions for using composer
Use composer to install this extension. First make sure that Magento is installed via composer, and that there is a valid `composer.json` file present.

Next, install our module using the following command:

    composer require yireo/magento2-newrelic2

Next, install the new module into Magento itself:

    ./bin/magento module:enable Yireo_NewRelic2
    ./bin/magento setup:upgrade

Check whether the module is succesfully installed in **Admin > Stores >
Configuration > Advanced > Advanced**.

Done.

## Instructions for manual copy
We recommend `composer` to install this package. However, if you want a manual copy instead, these are the steps:
* Make sure to install New Relic on your server first
* Make sure to have New Relics PHP module installed and active
* Upload the files in the `source/` folder to the folder `app/code/Yireo/NewRelic2` of your site
* Run `php -f bin/magento module:enable Yireo_NewRelic2`
* Run `php -f bin/magento setup:upgrade`
* Flush the Magento cache
* Configure settings under `Stores > Configuration > Advanced > Yireo New Relic`
* Done


