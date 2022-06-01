<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User\UserRole;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['UserRoleId'=>1,'RoleName'=>'Admin','DisplayName'=>'Admin','AddedBy'=>1,'IsMigrationData'=>true],
            ['UserRoleId'=>2,'RoleName'=>'Student','DisplayName'=>'Candidate','AddedBy'=>1,'IsMigrationData'=>true],
        ];

        foreach($items as $item)
        {
            UserRole::create($item);
        }
    }
}
