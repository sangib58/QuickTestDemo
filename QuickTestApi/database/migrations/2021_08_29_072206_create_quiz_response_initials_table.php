<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizResponseInitialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizResponseInitials', function (Blueprint $table) {
            $table->increments('quizResponseInitialId');
            $table->integer('userId');
            $table->string('email');
            $table->integer('attemptCount');
            $table->boolean('isExamined')->default(false);
            $table->integer('quizTopicId');
            $table->longtext('quizTitle');
            $table->double('quizMark',8,2);
            $table->double('userObtainedQuizMark',8,2)->nullable();
            $table->double('quizTime',8,2);
            $table->string('timeTaken')->nullable();
            $table->timestamp('startTime')->nullable();
            $table->timestamp('endTime')->nullable();               
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
        Schema::dropIfExists('quizResponseInitials');
    }
}
