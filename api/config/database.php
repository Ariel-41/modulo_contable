<?php
return [
    'host' => getenv('DB_HOST') ?? 'localhost',
    'dbname' => getenv('DB_NAME') ?? 'modulo_contable',
    'user' => getenv('DB_USER') ?? 'root',
    'password' => getenv('DB_PASSWORD') ?? '',
    'charset' => 'utf8mb4',
];