<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User\Users;
use Illuminate\Support\Facades\Auth;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['UserId'=>1,'UserRoleId'=>1,'FullName'=>'John Doe','Email'=>'admin@exam-systems.com','Password'=>bcrypt('12345678'),'AddedBy'=>1,'IsMigrationData'=>true],
            ['UserId'=>2,'UserRoleId'=>2,'FullName'=>'Helen Smith','Email'=>'candidate@exam-systems.com','Password'=>bcrypt('12345678'),'AddedBy'=>1,'IsMigrationData'=>true],
        ];

        foreach($items as $item)
        {
            Users::create($item);
        }
    }
}
