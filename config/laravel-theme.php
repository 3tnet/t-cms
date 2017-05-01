<?php

return [
    'themes_path' => base_path('themes'),
    'config_file_name' => 'config.json',
    'default_theme' => 'default',
    'default_screenshot_name' => 'screenshot',
    'screenshot_route' => [
        'url' => 'laravel-theme',
        'name' => 'laravel-theme',
        'options' => [
            // middleware => 'auth',
        ],
    ]
];