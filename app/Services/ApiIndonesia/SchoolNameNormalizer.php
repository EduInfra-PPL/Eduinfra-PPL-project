<?php

namespace App\Services\ApiIndonesia;

/**
 * Kunci pencocokan nama sekolah antarsumber (mis. "SMAN 3 Bandung" dan "SMA NEGERI 3 BANDUNG").
 * Fungsi murni. Dua nama dianggap sama bila kuncinya sama.
 */
class SchoolNameNormalizer
{
    /** Awalan singkatan jenjang dipetakan ke bentuk baku. */
    private const PREFIXES = [
        'SDN' => 'SD', 'SDS' => 'SD', 'SDIT' => 'SD', 'SDI' => 'SD', 'SDK' => 'SD',
        'SD' => 'SD',
        'MIN' => 'MI', 'MIS' => 'MI', 'MIT' => 'MI', 'MI' => 'MI',
        'SMPN' => 'SMP', 'SMPS' => 'SMP', 'SMPIT' => 'SMP', 'SMP' => 'SMP',
        'MTSN' => 'MTS', 'MTSS' => 'MTS', 'MTS' => 'MTS',
        'SMAN' => 'SMA', 'SMAS' => 'SMA', 'SMAIT' => 'SMA', 'SMA' => 'SMA',
        'SMKN' => 'SMK', 'SMKS' => 'SMK', 'SMK' => 'SMK',
        'MAN' => 'MA', 'MAS' => 'MA', 'MA' => 'MA',
    ];

    /** Kata status kepemilikan yang tidak membedakan sekolah. */
    private const NOISE = ['NEGERI', 'SWASTA'];

    public function key(string $name): string
    {
        $text = mb_strtoupper(trim($name));
        $text = preg_replace('/[^\pL\pN\s]+/u', ' ', $text) ?? '';
        // "SMAN3" dan "SMAN 3" dianggap sama: pisahkan huruf dan angka yang menempel
        $text = preg_replace('/(\pL)(\pN)/u', '$1 $2', $text) ?? '';
        $tokens = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($tokens !== [] && isset(self::PREFIXES[$tokens[0]])) {
            $tokens[0] = self::PREFIXES[$tokens[0]];
        }

        $tokens = array_values(array_filter($tokens, fn (string $t): bool => ! in_array($t, self::NOISE, true)));

        // tanpa spasi agar "SMA3" dan "SMA 3" dianggap sama
        return implode('', $tokens);
    }
}
