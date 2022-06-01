<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizQuestions', function (Blueprint $table) {
            $table->increments('quizQuestionId');
            $table->integer('quizTopicId');
            $table->longtext('questionDetail');
            $table->integer('serialNo')->default(0);
            $table->double('perQuestionMark',5,2)->default(0);
            $table->integer('questionTypeId');
            $table->integer('questionLavelId');
            $table->integer('questionCategoryId');
            $table->longtext('optionA')->nullable();
            $table->longtext('optionB')->nullable();
            $table->longtext('optionC')->nullable();
            $table->longtext('optionD')->nullable();
            $table->longtext('optionE')->nullable();
            $table->longtext('correctOption')->nullable();
            $table->longtext('answerExplanation')->nullable();
            $table->longtext('imagePath')->nullable();
            $table->longtext('videoPath')->nullable();
            $table->boolean('isCodeSnippet')->default(false);
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
        Schema::dropIfExists('quizQuestions');
    }
}
