#!/usr/bin/env php
<?php

/**
 * Generates consolidated RBAC CSV exports from AdminRbacService.
 * Run: php scripts/generate-rbac-matrix.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AdminRbacService;
use Database\Seeders\AdminRbacMockUsersSeeder;

$matrix = AdminRbacService::roleModuleMatrix();
$roles = AdminRbacService::allRoleSlugs();
$modules = AdminRbacService::allMatrixKeys();

function writeFullMatrixSheet(string $path, string $product, array $roles, array $modules): void
{
    $handle = fopen($path, 'w');
    fputcsv($handle, [
        'Producto', 'Rol', 'Modulo', 'Seccion', 'En_Menu', 'Ver', 'Crear', 'Editar',
        'Eliminar', 'Aprobar', 'Exportar', 'Configurar', 'Virtual', 'Diferido',
    ]);

    foreach ($roles as $roleSlug) {
        if (AdminRbacService::roleModuleMatrix()[$roleSlug] === ['*']) {
            fputcsv($handle, [
                $product, $roleSlug, '*', 'Todos los módulos activos', '1',
                '1', '1', '1', '1', '1', '1', '1', '0', '0',
            ]);
            continue;
        }

        foreach ($modules as $moduleKey) {
            $flags = AdminRbacService::permissionFlagsForRoleOnModule($roleSlug, $moduleKey);
            fputcsv($handle, array_merge(
                [
                    $product,
                    $roleSlug,
                    $moduleKey,
                    AdminRbacService::matrixKeyLabel($moduleKey),
                    $flags['en_menu'],
                    $flags['ver'],
                    $flags['crear'],
                    $flags['editar'],
                    $flags['eliminar'],
                    $flags['aprobar'],
                    $flags['exportar'],
                    $flags['configurar'],
                    AdminRbacService::isVirtualMatrixKey($moduleKey) ? '1' : '0',
                    in_array($moduleKey, AdminRbacService::DEFERRED_MATRIX_KEYS, true) ? '1' : '0',
                ]
            ));
        }
    }

    fclose($handle);
}

function writeCredentials(string $path, string $product, string $panelUrl, string $password, string $domain): void
{
    $handle = fopen($path, 'w');
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
    foreach (AdminRbacMockUsersSeeder::mockUsers($domain) as $entry) {
        fputcsv($handle, [
            $product,
            $panelUrl,
            $entry['role'],
            $roleNames[$entry['role']] ?? $entry['role'],
            $entry['email'],
            $password,
            'Creado por AdminRbacMockUsersSeeder; políticas vía AdminRbacMatrixSeeder',
        ]);
    }
    fclose($handle);
}

function writeModuleCatalog(string $path, string $product, array $modules): void
{
    $handle = fopen($path, 'w');
    fputcsv($handle, ['Producto', 'Modulo', 'Seccion', 'Virtual', 'Diferido']);
    foreach ($modules as $moduleKey) {
        fputcsv($handle, [
            $product,
            $moduleKey,
            AdminRbacService::matrixKeyLabel($moduleKey),
            AdminRbacService::isVirtualMatrixKey($moduleKey) ? '1' : '0',
            in_array($moduleKey, AdminRbacService::DEFERRED_MATRIX_KEYS, true) ? '1' : '0',
        ]);
    }
    fclose($handle);
}

function appendFile(string $target, string $source, string $sectionTitle = ''): void
{
    if ($sectionTitle !== '') {
        file_put_contents($target, "\n{$sectionTitle}\n", FILE_APPEND);
    }
    file_put_contents($target, file_get_contents($source), FILE_APPEND);
    file_put_contents($target, "\n", FILE_APPEND);
}

$docsDir = $root.'/docs';
if (! is_dir($docsDir)) {
    mkdir($docsDir, 0755, true);
}

writeFullMatrixSheet($docsDir.'/RBAC-ZIMO.csv', 'ZIMO', $roles, $modules);
writeFullMatrixSheet($docsDir.'/RBAC-XISTI.csv', 'XISTI', $roles, $modules);
writeModuleCatalog($docsDir.'/RBAC-MODULES.csv', 'ZIMO', $modules);
writeCredentials(
    $docsDir.'/RBAC-CREDENTIALS-ZIMO.csv',
    'ZIMO',
    'https://admin.appzimo.com',
    AdminRbacMockUsersSeeder::DEFAULT_PASSWORD,
    AdminRbacMockUsersSeeder::DEFAULT_EMAIL_DOMAIN
);
writeCredentials(
    $docsDir.'/RBAC-CREDENTIALS-XISTI.csv',
    'XISTI',
    'https://admin.xistiapp.com',
    'PdP-Xisti-RBAC-2026!',
    'xistiapp.com'
);

$combined = $docsDir.'/RBAC-MATRIX.csv';
file_put_contents($combined, '');
appendFile($combined, $docsDir.'/RBAC-CREDENTIALS-ZIMO.csv', '=== ACCESOS PANEL — ZIMO ===');
appendFile($combined, $docsDir.'/RBAC-CREDENTIALS-XISTI.csv', '=== ACCESOS PANEL — XISTI ===');
appendFile($combined, $docsDir.'/RBAC-MODULES.csv', '=== CATÁLOGO DE SECCIONES ===');
appendFile($combined, $docsDir.'/RBAC-ZIMO.csv', '=== MATRIZ COMPLETA — ZIMO (todos los roles × todas las secciones) ===');
appendFile($combined, $docsDir.'/RBAC-XISTI.csv', '=== MATRIZ COMPLETA — XISTI (todos los roles × todas las secciones) ===');

echo "Generated RBAC CSV files in {$docsDir}\n";
echo 'Roles: '.count($roles).'; Modules: '.count($modules).'; Rows per product: '.(count($roles) * count($modules))."\n";
