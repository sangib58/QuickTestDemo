<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz\ReportType;

class ReportTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['ReportTypeId'=>1,'ReportTypeName'=>'Pending Examine'],
            ['ReportTypeId'=>2,'ReportTypeName'=>'Reports'],
        ];

        foreach($items as $item)
        {
            ReportType::create($item);
        }
    }
}
