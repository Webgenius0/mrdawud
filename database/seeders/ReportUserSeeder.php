<?php

namespace Database\Seeders;

use App\Models\ReportUser;
use App\Models\BlockUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpParser\Node\Stmt\Block;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

class ReportUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReportUser::create([
            'user_id' => 1,
            'reported_user_id' => 2,
            'report' => 'Report 1',
        ]);

        BlockUser::create([
            'user_id' => 1,
            'blocked_user_id' => 2,
            
        ]);
    }
}
