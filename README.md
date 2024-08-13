Manual Tech Test
------------

In order to save time I forked from [Symfony Demo Application][1] and built on top of that.

Apologies for not removing all the Symfony Demo Application logic.

My code can be found in `/src/Core`, `/src/Shared`, `/tests/Core`, `/tests/Shared`

I focused most of my efforts on mapping the domain and the associated commands and queries.

Given more time I would have improved the following:
- Implement domain events
- Request validation and parameter mapping
- Added controller and handler tests
- Improved error handling with proper HTTP response codes
- Added Logging
- Add a bff to get the responses from the product category and questionnaire apis
- Add phpcs and phpstan
- Add a docker-compose file

Requirements
------------

* PHP 8.2.0 or higher;
* php-cli, php-common, php-mbstring, php-sqlite3, php-xml
* PDO-SQLite PHP extension enabled;
* and the [usual Symfony application requirements][2].

Installation
------------

```bash
git clone git@github.com:adamdaley/manual-test.git adamdaley_manual_test
cd adamdaley_manual_test/
composer install
```

Usage
-----

[Download Symfony CLI][3] and run this command:

```bash
cd adamdaley_manual_test/
symfony serve
```

Then access the application in your browser at the given URL (<https://localhost:8000> by default).

Then click the browse backend button on the right hand side.

Login as `jane_admin` with password `kitten`.

For the sake of ease I've already created a product category and questionnaire via the fixtures `/src/DataFixtures/AppFixtures.php`

The product categories can be found [here][4] 
The questionnaire can be found [here][5]

Then you can visit this link with the relevant answerIds to see the recommended products.

Example 1:

Q1 - A1  
Q2 - A3  
Q2c - A3  
Q3 - A2  
Q4 - A5  
Q5 - A5  

Would give the following [Recommended Products][6]

Example 2:

Q1 - A1  
Q2 - A4
Q3 - A2
Q4 - A5
Q5 - A5

Would give the following [Recommended Products][7]

Tests
-----

Execute this command to run tests:

```bash
cd adamdaley_manual_test/
./bin/phpunit
```


[1]: https://github.com/symfony/demo/
[2]: https://symfony.com/doc/current/setup.html#technical-requirements
[3]: https://symfony.com/download
[4]: http://localhost:8000/en/admin/product-category/
[5]: http://localhost:8000/en/questionnaire/01914bd1-abab-713e-a7d0-ee7efaff4111
[6]: http://localhost:8000/en/questionnaire/01914bd1-abab-713e-a7d0-ee7efaff4111/recommended-products?answerIds[]=01914bd1-abb4-7065-a63a-5e4542fb3332&answerIds[]=01914bd1-abb8-734b-a521-cb6d539c64d4&answerIds[]=01914bd1-abbf-7f34-b3bf-d36bba53c696&answerIds[]=01914bd1-abc1-757a-9705-497c71933b40&answerIds[]=01914bd1-abc6-74bd-871d-5d385bb37132&answerIds[]=01914bd1-abcb-7111-9143-2fae5b94cd59
[7]: http://localhost:8000/en/questionnaire/01914bd1-abab-713e-a7d0-ee7efaff4111/recommended-products?answerIds[]=01914bd1-abb4-7065-a63a-5e4542fb3332&answerIds[]=01914bd1-abb9-7761-b2b2-04424de1face&answerIds[]=01914bd1-abc1-757a-9705-497c71933b40&answerIds[]=01914bd1-abc6-74bd-871d-5d385bb37132&answerIds[]=01914bd1-abcb-7111-9143-2fae5b94cd59