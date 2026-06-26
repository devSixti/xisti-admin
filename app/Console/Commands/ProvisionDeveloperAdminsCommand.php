<?php

namespace App\Console\Commands;

use App\Models\Admin;
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

            $admin = Admin::query()->firstOrNew(['email' => $dev['email']]);
            $admin->name = $dev['name'];
            $admin->password = Hash::make((string) $dev['password']);
            $admin->roles = 1;
            $admin->area_id = 0;
            $admin->is_restrict_admin = 0;
            $admin->admin_type = 's';
            $admin->save();

            $this->info("OK: {$dev['email']} (super admin, id {$admin->id})");
        }

        return self::SUCCESS;
    }
}
