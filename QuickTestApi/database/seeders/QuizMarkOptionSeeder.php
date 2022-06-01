<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz\QuizMarkOption;

class QuizMarkOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['QuizMarkOptionId'=>1,'QuizMarkOptionName'=>'Equal distribution'],
            ['QuizMarkOptionId'=>2,'QuizMarkOptionName'=>'No marks(Survey)'],
            ['QuizMarkOptionId'=>3,'QuizMarkOptionName'=>'Question wise set'],
        ];

        foreach($items as $item)
        {
            QuizMarkOption::create($item);
        }
    }
}
