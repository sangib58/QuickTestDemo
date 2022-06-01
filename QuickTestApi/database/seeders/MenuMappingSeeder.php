<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu\MenuMapping;

class MenuMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['MenuMappingId'=>1,'UserRoleId'=>1,'AppMenuId'=>1,'AddedBy'=>1,'IsMigrationData'=>true],
            ['MenuMappingId'=>2,'UserRoleId'=>1,'AppMenuId'=>2,'AddedBy'=>1,'IsMigrationData'=>true],
            ['MenuMappingId'=>3,'UserRoleId'=>1,'AppMenuId'=>3,'AddedBy'=>1,'IsMigrationData'=>true],
            ['MenuMappingId'=>4,'UserRoleId'=>1,'AppMenuId'=>4,'AddedBy'=>1,'IsMigrationData'=>true],
            ['MenuMappingId'=>5,'UserRoleId'=>1,'AppMenuId'=>5,'AddedBy'=>1,'IsMigrationData'=>true],
            ['MenuMappingId'=>6,'UserRoleId'=>1,'AppMenuId'=>6,'AddedBy'=>1,'IsMigrationData'=>true],
            ['MenuMappingId'=>7,'UserRoleId'=>1,'AppMenuId'=>7,'AddedBy'=>1,'IsMigrationData'=>true],
            ['MenuMappingId'=>8,'UserRoleId'=>1,'AppMenuId'=>9,'AddedBy'=>1,'IsMigrationData'=>true],
            ['MenuMappingId'=>9,'UserRoleId'=>1,'AppMenuId'=>10,'AddedBy'=>1,'IsMigrationData'=>true],
            ['MenuMappingId'=>10,'UserRoleId'=>1,'AppMenuId'=>11,'AddedBy'=>1,'IsMigrationData'=>true],
            ['MenuMappingId'=>12,'UserRoleId'=>2,'AppMenuId'=>1,'AddedBy'=>1,'IsMigrationData'=>true],
            ['MenuMappingId'=>13,'UserRoleId'=>2,'AppMenuId'=>8,'AddedBy'=>1,'IsMigrationData'=>true],
        ];

        foreach($items as $item)
        {
            MenuMapping::create($item);
        }
    }
}
