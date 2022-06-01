<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siteSettings', function (Blueprint $table) {
            $table->increments('siteSettingsId');
            $table->longtext('siteTitle');
            $table->longtext('welComeMessage')->nullable();    
            $table->longtext('copyRightText')->nullable();          
            $table->longtext('logoPath')->nullable();
            $table->longtext('faviconPath')->nullable();
            $table->string('appBarColor')->nullable();
            $table->string('footerColor')->nullable();
            $table->string('bodyColor')->nullable();
            $table->boolean('allowWelcomeEmail')->default(true);
            $table->boolean('allowFaq')->default(true);
            $table->boolean('endExam')->default(true);
            $table->boolean('logoOnExamPage')->default(true);
            $table->boolean('paidRegistration')->default(true);
            $table->integer('registrationPrice')->nullable();
            $table->string('currency')->nullable();
            $table->string('currencySymbol')->nullable();
            $table->longtext('stripePubKey')->nullable();
            $table->longtext('stripeSecretKey')->nullable();
            $table->longtext('clientUrl')->nullable();
            $table->string('defaultEmail')->nullable();
            $table->string('host')->nullable();
            $table->string('password')->nullable();  
            $table->string('port')->nullable();
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
        Schema::dropIfExists('siteSettings');
    }
}
