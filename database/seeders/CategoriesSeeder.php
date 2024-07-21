<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\Websites;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $category = Categories::create([
                'name' => 'Category '.$i,
                'description' => 'Category '.$i.' Description',
            ]);


            if ($category) {
                $website = Websites::create([
                    'name' => 'Website '.$i,
                    'url' => "http://website-$i.loc",
                    'description' => 'Website '.$i.' Description',
                ]);
                $website->categories()->attach([$category->id]);
            }
        }
    }
}
