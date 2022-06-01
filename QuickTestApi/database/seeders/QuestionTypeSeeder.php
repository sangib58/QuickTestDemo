<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question\QuestionType;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['QuestionTypeId'=>1,'QuestionTypeName'=>'MCQ'],
            ['QuestionTypeId'=>2,'QuestionTypeName'=>'Descriptive'],
        ];

        foreach($items as $item)
        {
            QuestionType::create($item);
        }
    }
}
