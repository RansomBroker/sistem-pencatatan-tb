<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Server Requirements
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel server requirements, you can add as many
    | as your application require, we check if the extension is enabled
    | by looping through the array and run "extension_loaded" on it.
    |
    */
    'core' => [
        'minPhpVersion' => '8.0.0',
    ],
    'requirements' => [
        'php' => [
            'openssl',
            'pdo',
            'mbstring',
            'tokenizer',
            'json',
            'curl',
            'pgsql',
            'pdo_pgsql',
            'sodium',
            'zip',
            'gd',
            'exif'
        ],
        'apache' => [
            'mod_rewrite',
        ],
    ],
];
