# GIATA Coding Challenge


How many Hotels in the GIATA Drive Hotel Directory have a GIATA-ID that can be divided by
its rating without remainder? (Only consider ratings between 2 and 7)

## Dependencies
* [PHP-7.4.33](https://www.php.net/)
* [phpunit-8.5](https://phpunit.de/documentation.html)
* [guzzle-7.0](https://docs.guzzlephp.org/en/stable/)

## Debugging
* _PHP logs_ : `tail -f /var/log/apache2/error.log`
* _Xdebug-2.9.2_: from debugging and evaluating objects

## Running Tests
* Install composer packages -  `composer install`
* Run Unit Tests with `./vendor/bin/phpunit test/HotelDirectoryRemainderTest.php --log-junit test.xml`

## Environment Variables
* _GIATA Drive Hotel Directory Link_ : DRIVE_URL
* _Batch to execute asyncronusly using guzzle_ : BATCH_SIZE