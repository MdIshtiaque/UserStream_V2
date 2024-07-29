<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Increase memory limit for large seeding
        ini_set('memory_limit', '512M');

        // Disable Eloquent events to speed up the process
        Model::unsetEventDispatcher();

        $batchSize = 100; // Define the chunk size for batch inserts
        $totalRecords = 20000; // Total number of records to insert

        for ($i = 0; $i < $totalRecords; $i += $batchSize) {
            $usersData = [];

            for ($j = 0; $j < $batchSize; $j++) {
                $usersData[] = [
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'), // Default password
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert each chunk
            DB::table('users')->insert($usersData);

            // Output progress to the console
            $completed = $i + $batchSize;
            echo "Inserted {$completed} records...\n";
        }

        // Re-enable Eloquent events
        Model::setEventDispatcher(app('events'));
    }
}
