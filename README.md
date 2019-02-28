# Laravel Anonymised SQL dumps

Essentially a wrapper around [https://github.com/ifsnop/mysqldump-php](ifsnop/mysqldump-php) that allows you to specify what columns to anonymise using [https://github.com/fzaninotto/Faker](Faker)

Helpful tool to debug production DBs while staying GDPR compliant.

## Installation

You can install the package via Composer:
```
composer require heyday/laravel-anonymised-sql-dumps
```

In Laravel 5.5 and above, the package should autoregister the service provider. For Laravel 5.4 or below you must install this service provider to `config/app.php`:
```
'providers' => [
    // ...
    Heyday\AnonymisedSQLDumps\AnonymisedSQLDumpsServiceProvider::class,
];
```

Publish the config file:
```
php artisan vendor:publish --provider="Heyday\AnonymisedSQLDumps\AnonymisedSQLDumpsServiceProvider" --tag="config"
```

Update the published config file (`config/anonymised-sql-dumps.php`) to match your DB structure (only the columns you want to anonymise) and specify a Faker property to replace original data, eg:

```
<?php

/**
 * mapping the DB columns to anonymise with faker
 * (see list of properties available:
 * vendor/fzaninotto/faker/src/Faker/Generator.php)
 *
 * 'TableName' => [
 *     'column' => 'fakerProperty'
 *      ]
 * if JSON, nest one deeper
 * eg
 *
 *     'adults' => [
 *         'name' => 'name', //normal column
 *         'data' => [ //json column
 *            'last_name' => 'lastName'
 *         ]
 *      ]
 */

return [
    'users' => [
        'last_name'      => 'lastName',
        'first_name'     => 'firstName',
        'phone_number'   => 'phoneNumber',
        'email'          => 'email'
    ]
];
```

## Usage

An artisan command should now be available.

The command to run is `anonymised-db-dumps:export` and it takes one optional argument which is the name of the dump file.

The generated .sql file will be available in `storage/dbdumps`.

## TODO

* Make the DB connection as an option.
* Make the destination disk/path as an option.
* Allow more nesting for JSON columns.
* Automatic detection of common fields? (email, address, name, etc...)