<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizTopics', function (Blueprint $table) {
            $table->increments('quizTopicId');
            $table->longtext('quizTitle');
            $table->double('quizTime',8,2)->default(0);
            $table->double('quizTotalMarks',8,2)->default(0);
            $table->double('quizPassMarks',8,2)->default(0);
            $table->integer('quizMarkOptionId');
            $table->integer('quizParticipantOptionId');
            $table->integer('certificateTemplateId')->nullable();
            $table->boolean('allowMultipleInputByUser')->default(false);
            $table->boolean('allowMultipleAnswer')->default(false);
            $table->boolean('allowMultipleAttempt')->default(false);
            $table->boolean('allowCorrectOption')->default(false); 
            $table->date('quizscheduleStartTime')->nullable();
            $table->date('quizscheduleEndTime')->nullable();
            $table->integer('quizPrice')->default(0);
            $table->boolean('isRunning')->default(false);
            $table->longtext('categories')->default('');            
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
        Schema::dropIfExists('quizTopics');
    }
}
