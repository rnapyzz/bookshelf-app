<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($users as $user) {
            DB::transaction(function () use ($user, $books) {
                $randomBooks = $books->random(rand(3, 5));

                $user->favoriteBooks()->syncWithoutDetaching($randomBooks->pluck('id'));
            });
        }
    }
}
