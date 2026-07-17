<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = Review::all();
        $users = User::all();

        foreach ($reviews as $review) {
            $potentialLikers = $users->where('id', '!=', $review->user_id);

            $likers = $potentialLikers->random(rand(0, 3));

            foreach ($likers as $user) {
                $review->likedByUsers()->syncWithoutDetaching([$user->id]);
            }

        }
    }
}
