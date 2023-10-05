<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Grid;

class GridsTableSeeder extends Seeder
{
    public function run()
    {
        foreach(range(1, 2500) as $index) {
            Grid::create([
                'clicked' => false,  // Assuming this is a boolean column to check if a grid has been clicked
                'reward_item_id' => (rand(1, 100) <= 5) ? rand(1, 10) : null  // Let's say there's a 5% chance a grid will have a reward with an id between 1 and 10
            ]);
        }
    }
}