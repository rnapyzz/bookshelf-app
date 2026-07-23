<?php

namespace Database\Seeders;

use App\Enums\ReviewComment;
use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    private const MIN_REVIEWS_PER_BOOK = 2;

    private const MAX_REVIEWS_PER_BOOK = 4;

    private const MIN_RATING = 1;

    private const MAX_RATING = 5;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($books as $book) {
            $reviewCount = rand(self::MIN_REVIEWS_PER_BOOK, self::MAX_REVIEWS_PER_BOOK);
            $selectedUsers = $users->random(min($reviewCount, $users->count()));

            foreach ($selectedUsers as $user) {
                $this->createReview($user, $book);
            }
        }
    }

    /**
     * レビューを作成するヘルパー関数
     */
    private function createReview($user, $book): void
    {
        $rating = rand(self::MIN_RATING, self::MAX_RATING);
        Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => $rating,
            'comment' => ReviewComment::fromInt($rating),
        ]);
    }
}
