<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question\QuestionLavel;

class QuestionLavelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['QuestionLavelId'=>1,'QuestionLavelName'=>'Easy'],
            ['QuestionLavelId'=>2,'QuestionLavelName'=>'Medium'],
            ['QuestionLavelId'=>3,'QuestionLavelName'=>'Hard'],
        ];

        foreach($items as $item)
        {
            QuestionLavel::create($item);
        }
    }
}
