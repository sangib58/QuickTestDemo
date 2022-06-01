<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz\QuizParticipantOption;

class QuizParticipantOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['QuizParticipantOptionId'=>1,'QuizParticipantOptionName'=>'All Registered Students'],
            ['QuizParticipantOptionId'=>2,'QuizParticipantOptionName'=>'Custom Input'],
        ];

        foreach($items as $item)
        {
            QuizParticipantOption::create($item);
        }
    }
}
