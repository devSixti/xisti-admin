<?php

namespace Database\Seeders;

use App\Helpers\XistiVehicleVariantHelper;
use App\Models\User;
use App\Support\QaSyntheticAssets;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * QA passenger + one driver per XISTI vehicle matrix variant (OTP 123456).
 */
class XistiQaTestUserSeeder extends Seeder
{
    public const QA_PHONE_LOCAL = '3001234567';

    public const QA_DRIVER_PHONE_LOCAL = '3009876543';

    public const QA_COUNTRY_CODE = '+57';

    public const QA_OTP = '123456';

    /** @var list<array<string, mixed>> */
    public const DRIVER_PROFILES = [
        [
            'variant' => XistiVehicleVariantHelper::CARRO_ECONOMICO,
            'phone' => self::QA_DRIVER_PHONE_LOCAL,
            'first_name' => 'Carlos',
            'last_name' => 'Mendoza',
            'email' => 'qa.conductor@xistiapp.com',
            'manufacturer' => 'Chevrolet',
            'model' => 'Spark GT',
            'year' => 2022,
            'color' => 'Blanco',
            'plate' => 'MDE123',
            'service_id' => 1,
            'is_taxi' => 1,
            'accept_transport' => 1,
            'accept_delivery' => 1,
            'accept_encomiendas' => 1,
            'also_transport_passengers' => 1,
        ],
        [
            'variant' => XistiVehicleVariantHelper::CARRO_ECO,
            'phone' => '3189274610',
            'first_name' => 'Lucia',
            'last_name' => 'Torres',
            'email' => 'qa.carro.eco@xistiapp.com',
            'manufacturer' => 'Renault',
            'model' => 'Kwid E-Tech',
            'year' => 2024,
            'color' => 'Verde',
            'plate' => 'ECO456',
            'service_id' => 1,
            'is_taxi' => 1,
            'accept_transport' => 1,
            'accept_delivery' => 1,
            'accept_encomiendas' => 1,
            'also_transport_passengers' => 1,
        ],
        [
            'variant' => XistiVehicleVariantHelper::CARRO_COMODO,
            'phone' => '3156748293',
            'first_name' => 'Mateo',
            'last_name' => 'Herrera',
            'email' => 'qa.carro.comodo@xistiapp.com',
            'manufacturer' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2023,
            'color' => 'Gris',
            'plate' => 'CMD789',
            'service_id' => 1,
            'is_taxi' => 0,
            'accept_transport' => 1,
            'accept_delivery' => 1,
            'accept_encomiendas' => 1,
            'also_transport_passengers' => 1,
        ],
        [
            'variant' => XistiVehicleVariantHelper::MOTO_BAJO,
            'phone' => '3128495761',
            'first_name' => 'Santiago',
            'last_name' => 'Ruiz',
            'email' => 'qa.moto.bajo@xistiapp.com',
            'manufacturer' => 'Honda',
            'model' => 'Wave 110',
            'year' => 2021,
            'color' => 'Rojo',
            'plate' => 'MTB12A',
            'service_id' => 3,
            'is_taxi' => 0,
            'accept_transport' => 1,
            'accept_delivery' => 1,
            'accept_encomiendas' => 1,
            'also_transport_passengers' => 1,
        ],
        [
            'variant' => XistiVehicleVariantHelper::MOTO_ALTO,
            'phone' => '3197584620',
            'first_name' => 'Daniela',
            'last_name' => 'Castro',
            'email' => 'qa.moto.alto@xistiapp.com',
            'manufacturer' => 'BMW',
            'model' => 'G 310 R',
            'year' => 2022,
            'color' => 'Negro',
            'plate' => 'MTA45B',
            'service_id' => 3,
            'is_taxi' => 0,
            'accept_transport' => 1,
            'accept_delivery' => 1,
            'accept_encomiendas' => 1,
            'also_transport_passengers' => 1,
        ],
        [
            'variant' => XistiVehicleVariantHelper::MOTO_MEDIO,
            'phone' => '3164839207',
            'first_name' => 'Nicolas',
            'last_name' => 'Pardo',
            'email' => 'qa.moto.medio@xistiapp.com',
            'manufacturer' => 'Yamaha',
            'model' => 'FZ 150',
            'year' => 2020,
            'color' => 'Azul',
            'plate' => 'MTM78C',
            'service_id' => 3,
            'is_taxi' => 0,
            'accept_transport' => 1,
            'accept_delivery' => 1,
            'accept_encomiendas' => 1,
            'also_transport_passengers' => 1,
        ],
        [
            'variant' => XistiVehicleVariantHelper::BICICLETA,
            'phone' => '3145928673',
            'first_name' => 'Paula',
            'last_name' => 'Gomez',
            'email' => 'qa.bicicleta@xistiapp.com',
            'manufacturer' => 'Specialized',
            'model' => 'Sirrus',
            'year' => 2024,
            'color' => 'Morado',
            'plate' => 'BCI001',
            'service_id' => 3,
            'is_taxi' => 0,
            'accept_transport' => 0,
            'accept_delivery' => 1,
            'accept_encomiendas' => 1,
            'also_transport_passengers' => 0,
        ],
    ];

    /** @return list<string> */
    public static function qaDriverPhones(): array
    {
        return array_values(array_unique(array_map(
            static fn (array $profile): string => (string) $profile['phone'],
            self::DRIVER_PROFILES
        )));
    }

    /** @return list<string> */
    public static function qaPhoneLocals(): array
    {
        return array_values(array_unique(array_merge(
            [self::QA_PHONE_LOCAL],
            self::qaDriverPhones()
        )));
    }

    public function run(): void
    {
        if (! $this->shouldRun()) {
            $this->command?->warn('XistiQaTestUserSeeder skipped (set XISTI_SEED_QA_USER=1 to run in production).');

            return;
        }

        $rider = $this->seedRider();
        $this->command?->info('XISTI QA users ready.');
        $this->command?->info('  Rider: '.self::QA_COUNTRY_CODE.' '.self::QA_PHONE_LOCAL.' (id '.$rider->id.')');

        foreach (self::DRIVER_PROFILES as $profile) {
            $driver = $this->seedDriverProfile($profile);
            $label = XistiVehicleVariantHelper::labelFor((string) $profile['variant']);
            $this->command?->info(sprintf(
                '  Driver %s: +57 %s (id %d)',
                $label,
                $profile['phone'],
                $driver->id
            ));
        }

        $this->command?->info('  OTP for all QA accounts: '.self::QA_OTP);
    }

    private function seedRider(): User
    {
        $user = User::query()
            ->where('contact_number', self::QA_PHONE_LOCAL)
            ->whereIn('country_code', ['+57', '57'])
            ->whereNull('deleted_at')
            ->orderByDesc('is_register')
            ->orderByDesc('id')
            ->first();

        if ($user === null) {
            $user = new User();
            $user->contact_number = self::QA_PHONE_LOCAL;
            $user->country_code = self::QA_COUNTRY_CODE;
            $user->login_type = 'email';
            $user->status = 1;
            $user->save();
        }

        $user->first_name = 'Laura';
        $user->last_name = 'Vega';
        $user->email = 'qa.pasajero@xistiapp.com';
        $user->login_type = 'email';
        $user->country_code = self::QA_COUNTRY_CODE;
        $user->contact_number = self::QA_PHONE_LOCAL;
        $user->currency = 'COP';
        $user->language = 'es';
        $user->status = 1;
        $user->is_register = 1;
        $user->verified_at = now();
        $user->fix_user_show = 1;
        $user->is_default_user = 1;
        $user->active_mode = 1;
        $user->is_driver_type = 0;
        $user->device_token = $user->device_token ?: 'qa-rider-device';
        $user->save();
        $user->generateAccessToken($user->id);
        if (empty($user->invite_code)) {
            $user->InviteCode($user->id, $user->first_name);
        }

        return $user;
    }

    /** @param  array<string, mixed>  $profile */
    private function seedDriverProfile(array $profile): User
    {
        $phone = (string) $profile['phone'];
        $variant = (string) $profile['variant'];
        $slug = str_replace('_', '-', $variant);

        $user = User::query()
            ->where('contact_number', $phone)
            ->whereIn('country_code', ['+57', '57'])
            ->whereNull('deleted_at')
            ->orderByDesc('is_register')
            ->orderByDesc('id')
            ->first();

        if ($user === null) {
            $user = new User();
            $user->contact_number = $phone;
            $user->country_code = self::QA_COUNTRY_CODE;
            $user->login_type = 'email';
            $user->status = 1;
            $user->save();
        }

        $label = XistiVehicleVariantHelper::labelFor($variant);
        $avatar = QaSyntheticAssets::ensureAvatarPng($slug, strtoupper(substr((string) $profile['first_name'], 0, 1).substr((string) $profile['last_name'], 0, 1)));
        $photos = QaSyntheticAssets::ensureVehiclePhotos($slug, $label);

        $user->first_name = (string) $profile['first_name'];
        $user->last_name = (string) $profile['last_name'];
        $user->email = (string) $profile['email'];
        $user->login_type = 'email';
        $user->country_code = self::QA_COUNTRY_CODE;
        $user->contact_number = $phone;
        $user->currency = 'COP';
        $user->language = 'es';
        $user->status = 1;
        $user->is_register = 1;
        $user->verified_at = now();
        $user->fix_user_show = 1;
        $user->is_default_user = 1;
        $user->is_driver_type = 1;
        $user->is_driver_status = 1;
        $user->driver_vehicle_status = 1;
        $user->driver_doc_status = 1;
        $user->driver_current_status = 1;
        $user->active_mode = 2;
        $user->device_token = $user->device_token ?: 'qa-driver-'.$slug;
        if (Schema::hasColumn('users', 'profile_image')) {
            $user->profile_image = $avatar;
        }
        $user->save();
        $user->generateAccessToken($user->id);
        if (empty($user->invite_code)) {
            $user->InviteCode($user->id, $user->first_name);
        }

        $serviceId = (int) $profile['service_id'];
        $vehicleTypeId = $this->resolveVehicleTypeId($serviceId, $variant);

        $payload = array_merge($photos, [
            'vehicle_type_id' => $vehicleTypeId,
            'delivery_variant' => $variant,
            'current_lat' => 4.6243,
            'current_long' => -74.0636,
            'vehicle_company' => (string) $profile['manufacturer'],
            'plat_no' => (string) $profile['plate'],
            'model_year' => (int) $profile['year'],
            'model_name' => (string) $profile['model'],
            'vehicle_color' => (string) $profile['color'],
            'doc_status' => 1,
            'accept_transport' => (int) $profile['accept_transport'],
            'accept_delivery' => (int) $profile['accept_delivery'],
            'accept_encomiendas' => (int) $profile['accept_encomiendas'],
            'also_transport_passengers' => (int) $profile['also_transport_passengers'],
            'is_taxi' => (int) $profile['is_taxi'],
            'search_distance_filter' => 50,
            'updated_at' => now(),
        ]);

        if ($serviceId === 1) {
            $payload['technical_inspection_expiry'] = now()->addYear()->format('Y-m-d');
        }

        if (DB::table('transport_driver_details')->where('user_id', $user->id)->exists()) {
            DB::table('transport_driver_details')->where('user_id', $user->id)->update($payload);
        } else {
            $payload['user_id'] = $user->id;
            $payload['created_at'] = now();
            DB::table('transport_driver_details')->insert($payload);
        }

        if (Schema::hasTable('vehicle_type_service_eligibility')) {
            DB::table('vehicle_type_service_eligibility')->updateOrInsert(
                ['vehicle_type_id' => $vehicleTypeId, 'service_id' => $serviceId],
                ['updated_at' => now(), 'created_at' => now()]
            );
            if ((int) $profile['accept_delivery'] === 1) {
                DB::table('vehicle_type_service_eligibility')->updateOrInsert(
                    ['vehicle_type_id' => $vehicleTypeId, 'service_id' => 4],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        $this->seedQaDriverDocuments((int) $user->id);

        return $user;
    }

    private function resolveVehicleTypeId(int $serviceId, string $variant): int
    {
        if ($variant === XistiVehicleVariantHelper::BICICLETA) {
            $id = (int) DB::table('transport_vehicle_type')
                ->where('service_id', 3)
                ->whereRaw('LOWER(name) LIKE ?', ['%bicicleta%'])
                ->where('status', 1)
                ->orderBy('id')
                ->value('id');
            if ($id > 0) {
                return $id;
            }
        }

        $id = (int) DB::table('transport_vehicle_type')
            ->where('service_id', $serviceId)
            ->where('status', 1)
            ->when($serviceId === 3, fn ($q) => $q->whereRaw('LOWER(name) NOT LIKE ?', ['%bicicleta%']))
            ->orderBy('id')
            ->value('id');

        if ($id > 0) {
            return $id;
        }

        return (int) DB::table('transport_vehicle_type')->where('status', 1)->orderBy('id')->value('id');
    }

    private function seedQaDriverDocuments(int $userId): void
    {
        if (! Schema::hasTable('required_documents') || ! Schema::hasTable('provider_documents')) {
            return;
        }

        $required = DB::table('required_documents')->where('status', 1)->get(['id', 'contains_expiry']);
        foreach ($required as $doc) {
            $filename = QaSyntheticAssets::ensureDocumentPng((int) $doc->id);
            $existing = DB::table('provider_documents')
                ->where('user_id', $userId)
                ->where('req_document_id', $doc->id)
                ->first();
            $row = [
                'document_file' => $filename,
                'status' => 1,
                'updated_at' => now(),
            ];
            if ((int) ($doc->contains_expiry ?? 0) === 1) {
                $row['expiry_date'] = now()->addYears(2)->format('Y-m-d');
            }
            if ($existing !== null) {
                DB::table('provider_documents')->where('id', $existing->id)->update($row);
                continue;
            }
            DB::table('provider_documents')->insert(array_merge($row, [
                'user_id' => $userId,
                'req_document_id' => $doc->id,
                'created_at' => now(),
            ]));
        }
    }

    private function shouldRun(): bool
    {
        if (env('XISTI_SEED_QA_USER') === '1' || env('XISTI_SEED_QA_USER') === 'true') {
            return true;
        }

        return app()->environment(['local', 'staging']);
    }
}
