<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appMenus', function (Blueprint $table) {
            $table->increments('appMenuId');
            $table->string('menuTitle');
            $table->longtext('url');
            $table->integer('sortOrder');
            $table->string('iconClass');
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
        Schema::dropIfExists('appMenus');
    }
}
