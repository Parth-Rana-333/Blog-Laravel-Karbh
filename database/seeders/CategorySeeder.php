<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        $date_time = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Business blogs',
                'created_at' => $date_time,
                'updated_at' => $date_time
            ],
            [
                'id' => 2,
                'name' => 'Fashion',
                'created_at' => $date_time,
                'updated_at' => $date_time
            ],
            [
                'id' => 3,
                'name' => 'Travel',
                'created_at' => $date_time,
                'updated_at' => $date_time
            ],
            [
                'id' => 4,
                'name' => 'DIY blogs',
                'created_at' => $date_time,
                'updated_at' => $date_time
            ],
            [
                'id' => 5,
                'name' => 'Fitness',
                'created_at' => $date_time,
                'updated_at' => $date_time
            ],
            [
                'id' => 6,
                'name' => 'Food blogs',
                'created_at' => $date_time,
                'updated_at' => $date_time
            ],
            [
                'id' => 7,
                'name' => 'Lifestyle',
                'created_at' => $date_time,
                'updated_at' => $date_time
            ],
            [
                'id' => 8,
                'name' => 'Music',
                'created_at' => $date_time,
                'updated_at' => $date_time
            ],
            [
                'id' => 9,
                'name' => 'Parenting blogs',
                'created_at' => $date_time,
                'updated_at' => $date_time
            ],
            [
                'id' => 10,
                'name' => 'Personal finance',
                'created_at' => $date_time,
                'updated_at' => $date_time
            ],
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
