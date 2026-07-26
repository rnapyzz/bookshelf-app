<?php

namespace Tests\Unit\Enums;

use App\Enums\ReviewComment;
use Tests\TestCase;

class ReviewCommentTest extends TestCase
{
    /** @test */
    public function 整数値から対応するレビューコメントを取得できる(): void
    {
        $this->assertEquals('あまりおすすめできない...', ReviewComment::fromInt(1));
        $this->assertEquals('いまいちでした', ReviewComment::fromInt(2));
        $this->assertEquals('普通です', ReviewComment::fromInt(3));
        $this->assertEquals('参考になりました', ReviewComment::fromInt(4));
        $this->assertEquals('とても良かったです', ReviewComment::fromInt(5));
    }

    /** @test */
    public function 定義外の整数値が渡された場合はデフォルトの普通ですを返す(): void
    {
        $this->assertEquals('普通です', ReviewComment::fromInt(0));
        $this->assertEquals('普通です', ReviewComment::fromInt(99));
    }
}
