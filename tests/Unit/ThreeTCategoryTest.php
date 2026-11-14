<?php

namespace Tests\Unit;

use App\Enums\ThreeTCategory;
use PHPUnit\Framework\TestCase;

class ThreeTCategoryTest extends TestCase
{
    public function test_column_names_match_regions_schema(): void
    {
        $this->assertSame('is_tertinggal', ThreeTCategory::Tertinggal->column());
        $this->assertSame('is_terdepan', ThreeTCategory::Terdepan->column());
        $this->assertSame('is_terluar', ThreeTCategory::Terluar->column());
    }

    public function test_values_lists_slugs_used_by_api_filters(): void
    {
        $this->assertSame(['tertinggal', 'terdepan', 'terluar'], ThreeTCategory::values());
    }

    public function test_is_truthy_accepts_pdo_pgsql_and_php_booleans(): void
    {
        $this->assertTrue(ThreeTCategory::isTruthy(true));
        $this->assertTrue(ThreeTCategory::isTruthy('t'));
        $this->assertTrue(ThreeTCategory::isTruthy('true'));
        $this->assertTrue(ThreeTCategory::isTruthy(1));
        $this->assertFalse(ThreeTCategory::isTruthy(false));
        $this->assertFalse(ThreeTCategory::isTruthy('f'));
        $this->assertFalse(ThreeTCategory::isTruthy(null));
        $this->assertFalse(ThreeTCategory::isTruthy(0));
    }

    public function test_active_for_returns_every_flagged_category_for_objects_and_arrays(): void
    {
        $row = (object) ['is_tertinggal' => 't', 'is_terdepan' => 'f', 'is_terluar' => true];

        $this->assertSame(
            [ThreeTCategory::Tertinggal, ThreeTCategory::Terluar],
            ThreeTCategory::activeFor($row)
        );
        $this->assertSame(
            [ThreeTCategory::Terluar],
            ThreeTCategory::activeFor(['is_tertinggal' => false, 'is_terdepan' => false, 'is_terluar' => true])
        );
    }

    public function test_label_joins_active_categories_and_is_null_when_none(): void
    {
        $this->assertSame(
            'Tertinggal / Terdepan',
            ThreeTCategory::labelFor(['is_tertinggal' => true, 'is_terdepan' => true, 'is_terluar' => false])
        );
        $this->assertNull(
            ThreeTCategory::labelFor(['is_tertinggal' => false, 'is_terdepan' => false, 'is_terluar' => false])
        );
    }

    public function test_decorate_normalises_booleans_and_adds_category_slugs(): void
    {
        $row = ThreeTCategory::decorate((object) [
            'is_3t' => 'f',
            'is_tertinggal' => false,
            'is_terdepan' => 't',
            'is_terluar' => false,
        ]);

        $this->assertFalse($row->is_3t);
        $this->assertSame(['terdepan'], $row->categories);
    }
}
