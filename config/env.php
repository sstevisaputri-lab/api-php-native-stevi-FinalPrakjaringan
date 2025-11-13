<?php
return [
    'db' => [
        'dsn'  => 'mysql:host=127.0.0.1;dbname=apiphp;charset=utf8mb4',
        'user' => 'root',
        'pass' => ''
    ],

    'app' => [
        'env' => 'local',
        'debug' => true,
        'base_url' => 'http://localhost/api-php-native-stevi/public',
        'jwt_secret' => 'stevi_saputri_master_dari_luwu_utara_secret_>=32_chars',
        'allowed_origins' => [
            'http://localhost:3000',
            'http://localhost'
        ]
    ]
];
