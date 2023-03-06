<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('id_ID');

        for($i = 0; $i < 20 ; $i++) {
           DB::table('products')->insert([
               'category_id' => random_int(1, 2),
               'product_id' => "Jasa-1",
               'name' => Str::random(20)
           ]);
        }
    }
}
