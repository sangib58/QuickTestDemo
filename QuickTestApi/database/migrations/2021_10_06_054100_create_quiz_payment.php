<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizPayments', function (Blueprint $table) {
            $table->increments('quizPaymentId');
            $table->integer('quizTopicId');
            $table->string('email');
            $table->string('currency');
            $table->integer('addedBy');
            $table->timestamp('dateAdded')->useCurrent();                                
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quizPayments');
    }
}
