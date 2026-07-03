<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ProvisionDeveloperAdminsCommand extends Command
{
    protected $signature = 'admin:provision-developers
        {--jeroni-password= : Contraseña para jeronimorestrepo48@gmail.com}
        {--nicolas-password= : Contraseña para alvarezmaciasnicolas@gmail.com}
        {--yhorman-password= : Contraseña para yhormangarcesballestas@gmail.com}';

    protected $description = 'Crea o actualiza cuentas super-admin para el equipo de desarrollo';

    public function handle(): int
    {
        $developers = [
            ['email' => 'jeronimorestrepo48@gmail.com', 'name' => 'Jerónimo Restrepo', 'password' => $this->option('jeroni-password')],
            ['email' => 'alvarezmaciasnicolas@gmail.com', 'name' => 'Nicolás Álvarez Macías', 'password' => $this->option('nicolas-password')],
            ['email' => 'yhormangarcesballestas@gmail.com', 'name' => 'Yhorman Garces', 'password' => $this->option('yhorman-password')],
        ];

        foreach ($developers as $dev) {
            if (empty($dev['password'])) {
                $this->error("Falta contraseña para {$dev['email']}");

                return self::FAILURE;
            }

            $role = AdminRole::query()->where('slug', 'desarrollador')->first();
            $admin = Admin::query()->firstOrNew(['email' => $dev['email']]);
            $admin->name = $dev['name'];
            $admin->password = Hash::make((string) $dev['password']);
            $admin->roles = 4;
            $admin->role_id = $role?->id;
            $admin->area_id = 0;
            $admin->is_restrict_admin = 1;
            $admin->admin_type = 's';
            $admin->status = 1;
            $admin->save();

            $this->info("OK: {$dev['email']} (developer RBAC, id {$admin->id})");
        }

        return self::SUCCESS;
    }
}
