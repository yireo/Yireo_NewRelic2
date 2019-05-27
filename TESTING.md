# Testing

## Unit testing
This extension ships with PHPUnit tests. The generic PHPUnit configuration in Magento 2 will pick up on these tests. To only
test Yireo extensions, copy the file `phpunit.xml.yireo` to your Magento folder `dev/tests/unit`. Next, from within that folder run PHPUnit. For instance:

    phpunit -c phpunit.xml.yireo --bootstrap PATH_TO_MAGENTO/dev/tests/unit/framework/bootstrap.php
