<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menuMappings', function (Blueprint $table) {
            $table->increments('menuMappingId');
            $table->integer('userRoleId');
            $table->integer('appMenuId');         
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
        Schema::dropIfExists('menuMappings');
    }
}
