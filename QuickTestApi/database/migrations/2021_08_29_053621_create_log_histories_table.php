<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logHistories', function (Blueprint $table) {
            $table->bigIncrements('logHistoryId');
            $table->string('logCode');
            $table->timestamp('logDate')->useCurrent();
            $table->integer('userId');
            $table->timestamp('logInTime')->useCurrent();
            $table->timestamp('logOutTime')->nullable(); 
            $table->string('ip')->nullable();
            $table->string('browser')->nullable();
            $table->string('browserVersion')->nullable();
            $table->string('platform')->nullable();        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logHistories');
    }
}
