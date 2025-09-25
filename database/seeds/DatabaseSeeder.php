<?php

use Database\Seeders\RoomChoicesSeeder;
use Database\Seeders\RoomChoicesSeeder2;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoomChoicesSeeder2::class);
        $this->call(RoomChoicesSeeder::class);
        $this->call(MoveStatusSeeder::class);
        $this->call(CompanyTypeSeeder::class);
        $this->call(CurrenciesSeeder::class);
        $this->call(AgentsSeeder::class);
        $this->call(DashboardCountSeeder::class);
        $this->call(CartonChoiceSeeder::class);
        $this->call(PackerCodeSeeder::class);
        $this->call(CartonConditionSeeder::class);
        $this->call(ConditionSideSeeder::class);
        $this->call(ItemLabelSeeder::class);
        $this->call(TermsAndConditionsSeeder::class);
        $this->call(ScreeningCategoriesSeeder::class);
        $this->call(TransloadCategoriesSeeder::class);
        $this->call(ContainerColorSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(ModelHasRoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SubscriptionSeeder::class);
        $this->call(RiskTitleSeeder::class);
    }
}
