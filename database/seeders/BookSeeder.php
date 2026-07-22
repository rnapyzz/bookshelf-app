<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $books = [
            [
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'isbn' => '9784101010014',
                'published_date' => '1905-01-01',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=1',
                'genres' => ['小説'],
                'description' => '1. 吾輩は猫である / 夏目漱石 / ISBN:9784101010014 / 1905-01-01 / ジャンル: 小説',
            ],
            [
                'title' => '人を動かす',
                'author' => 'D・カーネギー',
                'isbn' => '9784422100524',
                'published_date' => '1936-10-01',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=2',
                'genres' => ['ビジネス', '自己啓発'],
                'description' => '2. 人を動かす / D・カーネギー / ISBN:9784422100524 / 1936-10-01 / ジャンル: ビジネス, 自己啓発',
            ],
            [
                'title' => 'リーダブルコード',
                'author' => 'Dustin Boswell',
                'isbn' => '9784873115658',
                'published_date' => '2012-06-23',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=3',
                'genres' => ['技術書'],
                'description' => '3. リーダブルコード / Dustin Boswell / ISBN:9784873115658 / 2012-06-23 / ジャンル: 技術書',
            ],
            [
                'title' => '7つの習慣 ',
                'author' => 'スティーブン・R・コヴィー',
                'isbn' => '9784863940246',
                'published_date' => '2013-08-30',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=4',
                'genres' => ['ビジネス', '自己啓発'],
                'description' => '4. 7つの習慣 / スティーブン・R・コヴィー / ISBN:9784863940246 / 2013-08-30 / ジャンル: ビジネス, 自己啓発',
            ],
            [
                'title' => '坊っちゃん',
                'author' => '夏目漱石',
                'isbn' => '9784101010021',
                'published_date' => '1906-04-01',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=5',
                'genres' => ['小説'],
                'description' => '5. 坊っちゃん / 夏目漱石 / ISBN:9784101010021 / 1906-04-01 / ジャンル: 小説',
            ],
            [
                'title' => 'サピエンス全史',
                'author' => 'ユヴァル・ノア・ハラリ',
                'isbn' => '9784309226712',
                'published_date' => '2016-09-08',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=6',
                'genres' => ['歴史', '科学'],
                'description' => '6. サピエンス全史 / ユヴァル・ノア・ハラリ / ISBN:9784309226712 / 2016-09-08 / ジャンル: 歴史, 科学',
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '9784048930598',
                'published_date' => '2017-12-18',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=7',
                'genres' => ['技術書'],
                'description' => '7. Clean Code / Robert C. Martin / ISBN:9784048930598 / 2017-12-18 / ジャンル: 技術書',
            ],
            [
                'title' => '嫌われる勇気',
                'author' => '岸見一郎・古賀史健',
                'isbn' => '9784478025819',
                'published_date' => '2013-12-13',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=8',
                'genres' => ['自己啓発'],
                'description' => '8. 嫌われる勇気 / 岸見一郎・古賀史健 / ISBN:9784478025819 / 2013-12-13 / ジャンル: 自己啓発',
            ],
            [
                'title' => '火花',
                'author' => '又吉直樹',
                'isbn' => '9784163902302',
                'published_date' => '2015-03-11',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=9',
                'genres' => ['小説'],
                'description' => '9. 火花 / 又吉直樹 / ISBN:9784163902302 / 2015-03-11 / ジャンル: 小説',
            ],
            [
                'title' => 'FACTFULNESS',
                'author' => 'ハンス・ロスリング',
                'isbn' => '9784822289607',
                'published_date' => '2019-01-11',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=10',
                'genres' => ['ビジネス', '科学'],
                'description' => '10. FACTFULNESS / ハンス・ロスリング / ISBN:9784822289607 / 2019-01-11 / ジャンル: ビジネス, 科学',
            ],
            [
                'title' => 'コンテナ物語',
                'author' => 'マルク・レビンソン',
                'isbn' => '9784822251468',
                'published_date' => '2007-01-18',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=11',
                'genres' => ['ビジネス', '歴史'],
                'description' => '11. コンテナ物語 / マルク・レビンソン / ISBN:9784822251468 / 2007-01-18 / ジャンル: ビジネス, 歴史',
            ],
        ];

        foreach ($books as $book) {
            $genres = $book['genres'];
            unset($book['genres']);

            $book = Book::firstOrCreate(
                ['isbn' => $book['isbn']],
                array_merge($book, ['user_id' => $user->id])
            );

            $genreIds = Genre::whereIn('name', $genres)->pluck('id');
            $book->genres()->sync($genreIds);
        }
    }
}
