<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Permite CORS en todas las rutas de API y autenticación
    'allowed_methods' => ['*'], // Permite todos los métodos (GET, POST, PUT, DELETE, OPTIONS)
    'allowed_origins' => ['*'], // Permite cualquier origen (para desarrollo)
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Permite todas las cabeceras
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Permitir credenciales (necesario si usas tokens o cookies)
];
