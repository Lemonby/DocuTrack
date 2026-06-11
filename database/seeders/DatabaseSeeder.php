<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MasterDataSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            KriteriaSeeder::class,
            // inactivate academic activities
            // AcademicActivitiesSeeder::class,
        ]);
    }
}
