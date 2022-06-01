<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificateTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificateTemplates', function (Blueprint $table) {
            $table->increments('certificateTemplateId');
            $table->longtext('title');
            $table->longtext('heading');
            $table->longtext('mainText');
            $table->string('publishDate')->nullable();
            $table->longtext('topLeftImagePath')->nullable();
            $table->longtext('topRightImagePath')->nullable();
            $table->longtext('bottomMiddleImagePath')->nullable();
            $table->longtext('backgroundImagePath')->nullable();
            $table->longtext('leftSignatureText')->nullable();
            $table->longtext('leftSignatureImagePath')->nullable();
            $table->longtext('rightSignatureText')->nullable();
            $table->longtext('rightSignatureImagePath')->nullable();
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
        Schema::dropIfExists('certificateTemplates');
    }
}
