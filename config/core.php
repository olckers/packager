<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Core Saas API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for the Core components
    |
    | Parsable names in command manifest
    |
    | Specify the name attribute like this name=%pluralupperfirst%
    |
    | %name% - replace name as is
    | %plural% - replace name with plural version of name
    | %singular% - replace name with singular version of name
    | %uppercase% - replace name with uppercase version of name
    | %lowercase% - replace name with lowercase version of name
    | %upperfirst% - replace name with first letter of name in uppercase
    | %plurallowercase% - replace name with plural and all lowercase version of name
    | %pluraluppercase% - replace name with plural and all uppercase version of name
    | %pluralupperfirst% - replace name with plural and first letter of name in uppercase
    */

    'api_latest' => 1,
    'api_namespace' => 'Api',
    'command_settings' => [
        'silent' => true,
        'table' => true,
    ],
    'command_manifest' => [
        'scaffold_make_command' => [
            'make:model name=%upperfirst%',
            'make:migration name=create_%plurallowercase%_table',
            'make:exception name=%upperfirst%Exception',
            'make:event name=%upperfirst%Event',
            'make:listener name=%upperfirst%Listener',
            'make:resource name=%upperfirst%',
            'make:resource name=%upperfirst%Collection',
            'make:seeder name=%upperfirst%Seeder',
            'make:factory name=%upperfirst%Factory --model=%upperfirst%',
            'make:policy name=%upperfirst%Policy --model=%upperfirst%',
            'make:test name=%upperfirst%Test --unit=default',
            'make:test name=%upperfirst%Test',
            'make:notification name=%upperfirst%Notification',
            'make:request name=%upperfirst%Request',
            'make:controller name=%upperfirst%Controller',
            'make:provider name=%upperfirst%ServiceProvider',
            'make:middleware name=%upperfirst%Middleware',
            'make:observer name=%upperfirst%Observer --model=%upperfirst%',
            'make:repository name=%upperfirst%Repository',
            'make:repository-interface name=%upperfirst%RepositoryInterface'
        ],
        'package_make_command' => [],
        'package_scaffold_make_command' => [],
    ],
    'command_messages' => [
        'scaffold_make_command' => [
            'before' => [
                'info: Building scaffold for entity: %upperfirst%',
            ],
            'after' => [
                'comment: Add %upperfirst%Seeder in call list in DatabaseSeeder.php',
                'comment: Register %upperfirst%Repository and %upperfirst%RepositoryInterface in %upperfirst%ServiceProvider',
                'info: Scaffold for entity %upperfirst% generated. Review table output above for any errors.',
            ]
        ],
        'package_make_command' => [
            'before' => [],
            'after' => []
        ],
        'package_scaffold_make_command' => [
            'before' => [],
            'after' => []
        ],
    ]
];
