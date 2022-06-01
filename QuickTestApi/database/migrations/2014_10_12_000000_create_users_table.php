<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('userId');
            $table->integer('userRoleId');
            $table->string('fullName');
            $table->string('mobile')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->longtext('address')->nullable();
            $table->date('dateOfBirth')->nullable();
            $table->longtext('imagePath')->nullable();
            $table->string('stripeSessionId')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->boolean('isActive')->default(true);
            $table->boolean('isMigrationData')->default(false);
            $table->integer('addedBy')->nullable();
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
        Schema::dropIfExists('users');
    }
}
