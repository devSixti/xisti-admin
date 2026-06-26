<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
          AdminModuleSeeder::class,
          AdminPageActionSeeder::class,
          AppVersionSettingSeeder::class,
          EmailTemplatesSeeder::class,
          GeneralSettingsSeeder::class,
          LanguageConstatntSeeder::class,
          LanguageListsSeeder::class,
          PageSettingsSeeder::class,
          PushNotificationSeeder::class,
          RequiredDocumentsSeeder::class,
          ServiceSettingsSeeder::class,
          VehicleCommissionRateSeeder::class,
          SuperAdminSeeder::class,
          TopupWalletSeeder::class,
          TransportVehicleTypeSeeder::class,
          VehicleServicesSeeder::class,
          WorldCurrencySeeder::class,
        ]);

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);
    }
}
