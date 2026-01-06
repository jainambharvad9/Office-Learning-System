<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VideoCategory;

class VideoCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'GSTR',
                'description' => 'GST Return Filing training videos including GSTR-1, GSTR-3B, and other GST compliance procedures.',
                'is_active' => true,
            ],
            [
                'name' => 'ITR',
                'description' => 'Income Tax Return filing tutorials covering ITR-1, ITR-2, ITR-3, and ITR-4 forms.',
                'is_active' => true,
            ],
            [
                'name' => 'TDS',
                'description' => 'Tax Deducted at Source training including quarterly statements and annual returns.',
                'is_active' => true,
            ],
            [
                'name' => 'Accounting',
                'description' => 'General accounting principles, bookkeeping, and financial statement preparation.',
                'is_active' => true,
            ],
            [
                'name' => 'Compliance',
                'description' => 'Legal compliance, regulatory requirements, and statutory filings.',
                'is_active' => true,
            ],
            [
                'name' => 'Tax Planning',
                'description' => 'Tax planning strategies, deductions, and financial planning for businesses.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            VideoCategory::create($category);
        }
    }
}
