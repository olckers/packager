<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Packager Configuration
    |--------------------------------------------------------------------------
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

    'packager_working_directory' => 'packages',
    'command_settings' => [
        'silent' => true,
        'table' => true,
        'max_attempts' => 2, //set to null for now max
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
            'make:seeder name=%upperfirst%TableSeeder',
            'make:factory name=%upperfirst%Factory --model=%upperfirst%',
            'make:policy name=%upperfirst%Policy --model=%upperfirst%',
            'make:test name=%upperfirst%Test --unit=default',
            'make:test name=%upperfirst%Test',
            'make:notification name=%upperfirst%Notification',
            'make:request name=%upperfirst%Request',
            'make:controller name=%upperfirst%ApiController --api',
            'make:controller name=%upperfirst%Controller --resource',
            'make:provider name=%upperfirst%ServiceProvider',
            'make:middleware name=%upperfirst%Middleware',
            'make:observer name=%upperfirst%Observer --model=%upperfirst%',
            'make:repository name=%upperfirst%Repository',
            'make:repository-interface name=%upperfirst%RepositoryInterface',
            'make:command name=%upperfirst%CrudCommand'
        ],
        'package_make_command' => [
            'packager:provider name=%upperfirst%ServiceProvider --package=%packager%',
            'packager:model name=%upperfirst% --package=%packager% --package=%packager%',
            'packager:controller name=%upperfirst%ApiController --api --package=%packager%',
            'packager:controller name=%upperfirst%Controller --resource --package=%packager%',
            'packager:policy name=%upperfirst%Policy --package=%packager% --model=%upperfirst%',
            'packager:resource name=%upperfirst% --package=%packager%',
            'packager:resource name=%upperfirst%Collection --package=%packager% --collection',
            'packager:request name=%upperfirst%Request --package=%packager%',
            'packager:observer name=%upperfirst%Observer --model=%upperfirst% --package=%packager%',
            'packager:notification name=%upperfirst%Notification --package=%packager%',
            'packager:event name=%upperfirst%Event --package=%packager%',
            'packager:listener name=%upperfirst%Listener --event=%upperfirst%Event --package=%packager%',
            'packager:exception name=%upperfirst%Exception --package=%packager% --report --render',
            'packager:channel name=%upperfirst%Channel --package=%packager%',
            'packager:test name=%upperfirst%Test --package=%packager%',
            'packager:test name=%upperfirst%Test --package=%packager% --unit',
            'packager:repository name=%upperfirst%Repository --package=%packager%',
            'packager:repository-interface name=%upperfirst%RepositoryInterface --package=%packager%',
            'packager:migration name=create_%plurallowercase%_table --package=%packager%',
            'packager:factory name=%upperfirst%Factory --package=%packager% --model=%upperfirst%',
            'packager:seeder name=%upperfirst%TableSeeder --package=%packager%',
            'packager:middleware name=%upperfirst% --package=%packager%',
            'packager:command name=%upperfirst%CrudCommand --package=%packager%',

        ],
        'package_scaffold_make_command' => [
            'packager:model name=%upperfirst% --package=%packager% --package=%packager%',
            'packager:controller name=%upperfirst%ApiController --api --package=%packager%',
            'packager:controller name=%upperfirst%Controller --resource --package=%packager%',
            'packager:policy name=%upperfirst%Policy --package=%packager% --model=%upperfirst%',
            'packager:resource name=%upperfirst% --package=%packager%',
            'packager:resource name=%upperfirst%Collection --package=%packager% --collection',
            'packager:request name=%upperfirst%Request --package=%packager%',
            'packager:observer name=%upperfirst%Observer --model=%upperfirst% --package=%packager%',
            'packager:notification name=%upperfirst%Notification --package=%packager%',
            'packager:event name=%upperfirst%Event --package=%packager%',
            'packager:listener name=%upperfirst%Listener --event=upperfirst%Event --package=%packager%',
            'packager:exception name=%upperfirst%Exception --package=%packager% --report --render',
            'packager:test name=%upperfirst%Test --package=%packager%',
            'packager:test name=%upperfirst%Test --package=%packager% --unit',
            'packager:repository name=%upperfirst%Repository --package=%packager%',
            'packager:repository-interface name=%upperfirst%RepositoryInterface --package=%packager%',
            'packager:migration name=create_%plurallowercase%_table --package=%packager%',
            'packager:factory name=%upperfirst%Factory --package=%packager% --model=%upperfirst%',
            'packager:seeder name=%upperfirst%TableSeeder --package=%packager%',
            'packager:middleware name=%upperfirst% --package=%packager%',
            'packager:command name=%upperfirst%CrudCommand --package=%packager%',
        ],
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
            'before' => [
                'info: Building package: %upperfirst%',
            ],
            'after' => [
                'comment: Run "composer dump-autoload"',
                'comment: Run "php artisan optimize:clear"',
                'comment: Run "php artisan list"',
                'comment: Add %upperfirst%ServiceProvider to config/app.php if not auto discovered.',
                'comment: You may have to register some of the files manually in the %upperfirst%ServiceProvider file.',
                'line: The following items are not included in the default manifest: Jobs, Mails. You can generate these if you need them.',
                'info: Package %upperfirst% generated. Review table output above for any errors'
            ]
        ],
        'package_scaffold_make_command' => [
            'before' => [
                'info: Building package scaffold for entity: %upperfirst%',
            ],
            'after' => [
                'comment: Run "composer dump-autoload"',
                'comment: Run "php artisan optimize:clear"',
                'line: Note only the most commonly used files are generated. Publish the config file and edit package_scaffold_make_command to change the manifest.',
                'info: Scaffold for package entity %upperfirst% generated. Review table output above for any errors'
            ]
        ],
    ],
    'directory_filter' => [
        'replace' => [
            '.',
            '..',
            'DS_Store'
        ],
        'replace_with' => [
            '',
            '',
            ''
        ]
    ]
];
