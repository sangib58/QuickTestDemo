<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Others\SiteSetting;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['SiteSettingsId'=>1,'SiteTitle'=>'Quick Test','WelComeMessage'=>'Hello there,Sign in to start your task!','CopyRightText'=>'© 2021 Copyright Quick Test','DefaultEmail'=>'',
            'Host'=>'smtp.gmail.com','Password'=>'','Port'=>587,'LogoPath'=>'','FaviconPath'=>'','AppBarColor'=>'','FooterColor'=>'','BodyColor'=>'','AllowWelcomeEmail'=>true,
            'AllowFaq'=>true,'EndExam'=>true,'LogoOnExamPage'=>true,'PaidRegistration'=>true,'RegistrationPrice'=>0,'Currency'=>null,
            'CurrencySymbol'=>null,'StripePubKey'=>null,'StripeSecretKey'=>null,'ClientUrl'=>'','AddedBy'=>1,'IsMigrationData'=>true]
        ];

        foreach($items as $item)
        {
            SiteSetting::create($item);
        }
    }
}
