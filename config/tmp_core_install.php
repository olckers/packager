<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Core Saas API Install manifest
    |--------------------------------------------------------------------------
    |
    | DO NOT ALTER THIS FILE UNLESS YOU HAVE MADE CHANGES TO THE PACKAGE.
    | THIS FILE WILL BE COPIED TO THE CONFIG DIRECTORY TEMPORARILY
    | AND WILL BE DELETED FROM THE CONFIG DIRECTORY AFTER INSTALL COMPLETES.
    |
    | Here you may configure your install settings for the Core components
    |
    | The commands array contains fully qualified Artisan command as you would
    | enter it on the command line
    |
    | The wildcard character * can only be used on the file name
    | and NOT as part of the path.
    |
    */

    'silent' => true,
    'provider' => 'olckerstech\\core\\src\\CoreServiceProvider',
    'delete_before' => [
        '.env',
        'app/User.php',
        'database/factories/UserFactory.php',
        'database/migrations/*create_users_table.php',
        'database/migrations/*create_sessions_table.php'
    ],
    'delete_after' => [
        'config/tmp_core_install.php'
    ],
    'artisan_commands' => [
        'env',
        'core:delete --before',
        'vendor:publish --provider=provider --tag=env',
        'vendor:publish --provider=provider --tag=config',
        'vendor:publish --provider=provider --tag=user',
        'notifications:table',
        'cache:table',
        //'queue:table',
        'stub:publish',
        'key:generate',
        'core:delete --after',
        'optimize:clear',
        'inspire',
    ],
];
