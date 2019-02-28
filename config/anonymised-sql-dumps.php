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
 * Please update this file
 */

return [
    'users' => [
        'last_name'      => 'lastName',
        'first_name'     => 'firstName',
        'phone_number'   => 'phoneNumber',
        'email'          => 'email'
    ]
];