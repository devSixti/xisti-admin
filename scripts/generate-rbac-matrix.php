#!/usr/bin/env php
<?php

/**
 * Generates docs/RBAC-MATRIX.csv (ZIMO) from AdminRbacService matrix definition.
 * Run: php scripts/generate-rbac-matrix.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AdminRbacService;

$actions = ['Ver', 'Crear', 'Editar', 'Eliminar', 'Aprobar', 'Exportar', 'Configurar'];
$matrix = AdminRbacService::roleModuleMatrix();

function rowForRole(string $role, string $module, array $flags): array
{
    return array_merge([$role, $module], $flags);
}

function writeSheet(string $path, string $product, array $matrix): void
{
    $handle = fopen($path, 'w');
    fputcsv($handle, ['Producto', 'Rol', 'Modulo', 'Ver', 'Crear', 'Editar', 'Eliminar', 'Aprobar', 'Exportar', 'Configurar']);

    foreach ($matrix as $roleSlug => $modules) {
        if ($modules === ['*']) {
            fputcsv($handle, [$product, $roleSlug, '*', '1', '1', '1', '1', '1', '1', '1']);
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
            fputcsv($handle, array_merge([$product, $roleSlug, $module], $flags));
        }
    }
    fclose($handle);
}

$docsDir = $root.'/docs';
if (! is_dir($docsDir)) {
    mkdir($docsDir, 0755, true);
}

writeSheet($docsDir.'/RBAC-ZIMO.csv', 'ZIMO', $matrix);
writeSheet($docsDir.'/RBAC-SIXTY.csv', 'Sixty', $matrix);

// Combined workbook-friendly file (two sections)
$combined = $docsDir.'/RBAC-MATRIX.csv';
$out = fopen($combined, 'w');
fputcsv($out, ['=== RBAC ZIMO ===']);
fclose($out);
$zimo = file_get_contents($docsDir.'/RBAC-ZIMO.csv');
file_put_contents($combined, $zimo, FILE_APPEND);
file_put_contents($combined, "\n=== RBAC SIXTY ===\n", FILE_APPEND);
$sixty = file_get_contents($docsDir.'/RBAC-SIXTY.csv');
file_put_contents($combined, $sixty, FILE_APPEND);

echo "Generated:\n- {$docsDir}/RBAC-ZIMO.csv\n- {$docsDir}/RBAC-SIXTY.csv\n- {$docsDir}/RBAC-MATRIX.csv\n";
