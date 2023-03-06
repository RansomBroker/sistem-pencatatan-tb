<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('id_ID');

        for ($i = 0 ; $i < 500; $i++) {
            DB::table('branches')->insert([
                "name" => Str::random(12),
                "branch" => $faker->city,
                "address" => $faker->streetName,
                "telephone" => $faker->phoneNumber,
                'company' => "PT.". Str::random('12')
            ]);
        }
    }
}
