<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Others\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['FaqId'=>1,'Title'=>'What are the purposes of this app?','Description'=>'Quick Test will fulfill your need to take online Exams,Quizes as well as you can perform surveys by this app.','AddedBy'=>1,'IsMigrationData'=>true],
            ['FaqId'=>2,'Title'=>'What will be requirements to take a Test?','Description'=>'Nothing at all! You just need an active Email.','AddedBy'=>1,'IsMigrationData'=>true],
        ];

        foreach($items as $item)
        {
            Faq::create($item);
        }
    }
}
