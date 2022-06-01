<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu\AppMenu;

class AppMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['AppMenuId'=>1,'MenuTitle'=>'Dashboard','Url'=>'/dashboard','SortOrder'=>1,'IconClass'=>'dashboard','AddedBy'=>1,'IsMigrationData'=>true],
            ['AppMenuId'=>2,'MenuTitle'=>'Menus','Url'=>'/menu/menus','SortOrder'=>2,'IconClass'=>'menu_open','AddedBy'=>1,'IsMigrationData'=>true],
            ['AppMenuId'=>3,'MenuTitle'=>'Roles','Url'=>'/user/roles','SortOrder'=>3,'IconClass'=>'supervised_user_circle','AddedBy'=>1,'IsMigrationData'=>true],
            ['AppMenuId'=>4,'MenuTitle'=>'Users','Url'=>'/user/users','SortOrder'=>4,'IconClass'=>'mdi-account-multiple','AddedBy'=>1,'IsMigrationData'=>true],
            ['AppMenuId'=>5,'MenuTitle'=>'Category','Url'=>'/question/category','SortOrder'=>5,'IconClass'=>'category','AddedBy'=>1,'IsMigrationData'=>true],
            ['AppMenuId'=>6,'MenuTitle'=>'Assessments','Url'=>'/quiz/topics','SortOrder'=>6,'IconClass'=>'emoji_objects','AddedBy'=>1,'IsMigrationData'=>true],
            ['AppMenuId'=>7,'MenuTitle'=>'Questions','Url'=>'/question/quizes','SortOrder'=>7,'IconClass'=>'help_center','AddedBy'=>1,'IsMigrationData'=>true],
            ['AppMenuId'=>8,'MenuTitle'=>'Reports','Url'=>'/report/students','SortOrder'=>8,'IconClass'=>'description','AddedBy'=>1,'IsMigrationData'=>true],
            ['AppMenuId'=>9,'MenuTitle'=>'Certificate Template','Url'=>'/report/certificates','SortOrder'=>9,'IconClass'=>'card_giftcard','AddedBy'=>1,'IsMigrationData'=>true],
            ['AppMenuId'=>10,'MenuTitle'=>'Examine & Reports','Url'=>'/report/admin','SortOrder'=>10,'IconClass'=>'description','AddedBy'=>1,'IsMigrationData'=>true],
            ['AppMenuId'=>11,'MenuTitle'=>'App Settings','Url'=>'/settings/appSettings','SortOrder'=>11,'IconClass'=>'settings','AddedBy'=>1,'IsMigrationData'=>true],
        ];

        foreach($items as $item)
        {
            AppMenu::create($item);
        }
    }
}
