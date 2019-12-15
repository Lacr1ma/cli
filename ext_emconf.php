<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Command Line Interface',
    'description' => 'Extends typo3 core cli commands with additional functionality.',
    'category' => 'be',
    'author' => 'Borulko Serhii',
    'author_email' => 'borulkosergey@icloud.com',
    'state' => 'alpha',
    'clearCacheOnLoad' => true,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99'
        ]
    ]
];
