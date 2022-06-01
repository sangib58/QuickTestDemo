<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            UserRoleSeeder::class,
            AppMenuSeeder::class,
            MenuMappingSeeder::class,
            FaqSeeder::class,
            QuestionLavelSeeder::class,
            QuestionTypeSeeder::class,
            QuizMarkOptionSeeder::class,
            QuizParticipantOptionSeeder::class,
            ReportTypeSeeder::class,
            SiteSettingsSeeder::class
        ]);
    }
}
