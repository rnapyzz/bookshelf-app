<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    private const TOTAL_REVIEWS =32;
    private const MIN_REVIEWS_PER_BOOK = 2;
    private const MAX_REVIEWS_PER_BOOK = 4;
    private const MIN_RATING = 3;
    private const MAX_RATING = 5;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        // 最低レビュー数を各書籍に割り当てる
        $allocation = array_fill(0, $books->count(), self::MIN_REVIEWS_PER_BOOK);

        // 残りの必要数を、書籍のレビュー最大数を超えないようにランダムに割り振る
        $remaining = self::TOTAL_REVIEWS - array_sum($allocation);
        while ($remaining > 0) {
            $index = array_rand($allocation);
            if ($allocation[$index] < self::MAX_REVIEWS_PER_BOOK) {
                $allocation[$index]++;
                $remaining--;
            }
        }

        // 必要レビュー数ごとにランダムにレビューを作成する
        foreach ($books as $index => $book) {
            foreach ($users->random($allocation[$index]) as $user) {
                $this->createReview($user, $book);
            }
        }
    }

    private function createReview($user, $book): void
    {
        $comments = [
            'とってもよかったです',
            '勉強になりました',
            '参考になりました',
            'よくわかりませんでした',
            'おすすめできません',
        ];

        Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => rand(self::MIN_RATING, self::MAX_RATING),
            'comment' => $comments[array_rand($comments)],
        ]);
    }
}
