<?php

namespace App\Enums;

/**
 * Kategori 3T pada wilayah (kolom boolean di tabel regions).
 * Satu wilayah boleh memegang lebih dari satu kategori.
 * Kolom `is_3t` (turunan di database) = tier > 0 atau salah satu kategori bernilai true.
 */
enum ThreeTCategory: string
{
    case Tertinggal = 'tertinggal';
    case Terdepan = 'terdepan';
    case Terluar = 'terluar';

    /** Nama kolom boolean pada tabel regions. */
    public function column(): string
    {
        return 'is_'.$this->value;
    }

    public function label(): string
    {
        return match ($this) {
            self::Tertinggal => 'Tertinggal',
            self::Terdepan => 'Terdepan',
            self::Terluar => 'Terluar',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_map(fn (self $case): string => $case->value, self::cases());
    }

    /**
     * Nilai boolean dari baris database. PDO pgsql dapat mengembalikan 't'/'f' atau bool.
     */
    public static function isTruthy(mixed $value): bool
    {
        return $value === true || in_array($value, [1, '1', 't', 'true'], true);
    }

    /**
     * Daftar kategori yang aktif pada satu baris wilayah/sekolah (objek atau array).
     *
     * @return list<self>
     */
    public static function activeFor(object|array $row): array
    {
        $active = [];
        foreach (self::cases() as $case) {
            $value = is_array($row) ? ($row[$case->column()] ?? false) : ($row->{$case->column()} ?? false);
            if (self::isTruthy($value)) {
                $active[] = $case;
            }
        }

        return $active;
    }

    /** Label gabungan untuk tampilan, mis. "Tertinggal / Terdepan". Kosong bila bukan 3T. */
    public static function labelFor(object|array $row): ?string
    {
        $labels = array_map(fn (self $case): string => $case->label(), self::activeFor($row));

        return $labels === [] ? null : implode(' / ', $labels);
    }

    /**
     * Tambahkan `is_3t` (bool) dan `categories` (daftar slug) ke baris wilayah/sekolah
     * untuk respons API. Dipakai bersama oleh RegionController dan SchoolController.
     */
    public static function decorate(object $row): object
    {
        $row->is_3t = self::isTruthy($row->is_3t ?? false);
        $row->categories = array_map(fn (self $case): string => $case->value, self::activeFor($row));

        return $row;
    }
}
