<?php

namespace Database\Seeders;

use App\Models\NewsFeed;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsfeedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NewsFeed::create([
            'title' => 'News Feed 1',
            'description' => 'Description for News Feed 1',
            'location' => 'Location for News Feed 1',
            'image' => 'image.jpg',
        ]);

    }
}
