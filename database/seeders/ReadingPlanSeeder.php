<?php

namespace Database\Seeders;

use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReadingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();
        $plans = [
            [
                'book_id' => 1,
                'user_id' => 1,
                'target_date' => (clone $today)->addDays(3),
                'completed_at' => null,
                'status' => ReadingPlanStatus::NotStarted,
            ],
            [
                'book_id' => 2,
                'user_id' => 1,
                'target_date' => (clone $today)->addDays(3),
                'completed_at' => now(),
                'status' => ReadingPlanStatus::Completed,
            ],
            [
                'book_id' => 3,
                'user_id' => 1,
                'target_date' => (clone $today),
                'completed_at' => null,
                'status' => ReadingPlanStatus::NotStarted,
            ],
            [
                'book_id' => 4,
                'user_id' => 1,
                'target_date' => (clone $today),
                'completed_at' => now(),
                'status' => ReadingPlanStatus::Completed,
            ],
            [
                'book_id' => 5,
                'user_id' => 1,
                'target_date' => (clone $today)->subDays(),
                'completed_at' => null,
                'status' => ReadingPlanStatus::NotStarted,
            ],
            [
                'book_id' => 6,
                'user_id' => 1,
                'target_date' => (clone $today)->addDay(),
                'completed_at' => now(),
                'status' => ReadingPlanStatus::Completed,
            ],
            [
                'book_id' => 7,
                'user_id' => 1,
                'target_date' => (clone $today)->subDays(3),
                'completed_at' => null,
                'status' => ReadingPlanStatus::Expired,
            ],
            [
                'book_id' => 8,
                'user_id' => 1,
                'target_date' => (clone $today)->addDays(3),
                'completed_at' => now(),
                'status' => ReadingPlanStatus::Completed,
            ],
            [
                'book_id' => 3,
                'user_id' => 2,
                'target_date' => (clone $today),
                'completed_at' => null,
                'status' => ReadingPlanStatus::NotStarted,
            ],
        ];

        foreach ($plans as $plan) {
            ReadingPlan::updateOrCreate(
                [
                    'user_id' => $plan['user_id'],
                    'book_id' => $plan['book_id'],
                ],
                $plan
            );
        }
    }
}
