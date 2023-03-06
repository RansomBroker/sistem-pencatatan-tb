<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('id_ID');

        for ($i = 0; $i < 1000; $i++) {
            DB::table('customers')->insert([
                'customer_id' => mb_strtoupper(Str::random(2)).random_int(0000, 9999),
                'name' => $faker->name(),
                'nickname' => $faker->userName(),
                'phone' => $faker->phoneNumber(),
                'identity_number' => random_int(1111111111111111, 9999999999999999),
                'birth_date' => $faker->date('Y-m-d'),
                'address' => $faker->streetName(),
                'email' => $faker->email(),
            ]);
        }
    }
}
