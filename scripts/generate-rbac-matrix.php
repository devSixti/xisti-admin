#!/usr/bin/env php
<?php

declare(strict_types=1);

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AdminRbacService;
use Database\Seeders\AdminRbacMockUsersSeeder;

$matrix = AdminRbacService::roleModuleMatrix();
$docsDir = $root.'/docs';
if (! is_dir($docsDir)) {
    mkdir($docsDir, 0755, true);
}

$handle = fopen($docsDir.'/RBAC-XISTI.csv', 'w');
fputcsv($handle, ['Producto', 'Rol', 'Modulo', 'Ver', 'Crear', 'Editar', 'Eliminar', 'Aprobar', 'Exportar', 'Configurar']);
foreach ($matrix as $roleSlug => $modules) {
    if ($modules === ['*']) {
        fputcsv($handle, ['XISTI', $roleSlug, '*', '1', '1', '1', '1', '1', '1', '1']);
        continue;
    }
    foreach ($modules as $module) {
        $flags = match ($roleSlug) {
            'contabilidad' => ['1', '0', '0', '0', '0', '1', '0'],
            'aprobaciones' => ['1', '0', '0', '0', '1', '0', '0'],
            'marketing' => ['1', '1', '1', '0', '0', '0', '0'],
            'desarrollador' => ['1', '0', '0', '0', '0', '0', '1'],
            default => ['1', '1', '1', '0', '0', '0', '0'],
        };
        fputcsv($handle, array_merge(['XISTI', $roleSlug, $module], $flags));
    }
}
fclose($handle);

$handle = fopen($docsDir.'/RBAC-CREDENTIALS-XISTI.csv', 'w');
fputcsv($handle, ['Producto', 'Panel URL', 'Rol', 'Nombre rol', 'Email', 'Contraseña', 'Notas']);
$roleNames = [
    'admin_total' => 'Admin Total',
    'contabilidad' => 'Contabilidad',
    'socio' => 'Socio',
    'desarrollador' => 'Desarrollador',
    'aprobaciones' => 'Aprobaciones',
    'soporte' => 'Soporte',
    'marketing' => 'Marketing',
];
foreach (AdminRbacMockUsersSeeder::mockUsers() as $entry) {
    fputcsv($handle, [
        'XISTI',
        'https://admin.xistiapp.com',
        $entry['role'],
        $roleNames[$entry['role']] ?? $entry['role'],
        $entry['email'],
        AdminRbacMockUsersSeeder::DEFAULT_PASSWORD,
        'AdminRbacMatrixSeeder + AdminRbacMockUsersSeeder',
    ]);
}
fclose($handle);

echo "Generated {$docsDir}/RBAC-XISTI.csv and RBAC-CREDENTIALS-XISTI.csv\n";
