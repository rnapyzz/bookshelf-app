<?php

namespace Tests\Unit\Enums;

use App\Enums\ReadingPlanStatus;
use Tests\TestCase;

class ReadingPlanStatusTest extends TestCase
{
    /** @test */
    public function ステータスの値が正しく定義されている(): void
    {
        $this->assertEquals('not_started', ReadingPlanStatus::NotStarted->value);
        $this->assertEquals('expired', ReadingPlanStatus::Expired->value);
        $this->assertEquals('completed', ReadingPlanStatus::Completed->value);
    }

    /** @test */
    public function ステータスのラベルが正しく取得できる(): void
    {
        $this->assertEquals('未読', ReadingPlanStatus::NotStarted->label());
        $this->assertEquals('期限切れ', ReadingPlanStatus::Expired->label());
        $this->assertEquals('読了', ReadingPlanStatus::Completed->label());
    }

    /** @test */
    public function ステータスのバッジクラスが正しく取得できる(): void
    {
        $this->assertEquals('bg-gray-100 text-gray-800', ReadingPlanStatus::NotStarted->badgeClass());
        $this->assertEquals('bg-red-100 text-gray-800', ReadingPlanStatus::Expired->badgeClass());
        $this->assertEquals('bg-green-100 text-green-800', ReadingPlanStatus::Completed->badgeClass());
    }
}
