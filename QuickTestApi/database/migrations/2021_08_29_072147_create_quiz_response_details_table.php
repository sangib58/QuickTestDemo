<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizResponseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizResponseDetails', function (Blueprint $table) {
            $table->increments('quizResponseDetailId');
            $table->integer('quizResponseInitialId');
            $table->integer('quizQuestionId');
            $table->longtext('questionDetail');
            $table->longtext('userAnswer')->nullable();
            $table->boolean('isAnswerSkipped')->default(false);
            $table->longtext('correctAnswer')->nullable();
            $table->longtext('answerExplanation')->nullable();
            $table->double('questionMark',8,2)->default(0);
            $table->double('userObtainedQuestionMark',8,2)->default(0);
            $table->longtext('imagePath')->nullable();
            $table->longtext('videoPath')->nullable();
            $table->boolean('isExamined')->default(false);         
            $table->boolean('isActive')->default(true);
            $table->boolean('isMigrationData')->default(false);
            $table->integer('addedBy');
            $table->timestamp('dateAdded')->useCurrent();
            $table->integer('lastUpdatedBy')->nullable();
            $table->timestamp('lastUpdatedDate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quizResponseDetails');
    }
}
