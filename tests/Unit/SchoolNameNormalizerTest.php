<?php

namespace Tests\Unit;

use App\Services\ApiIndonesia\SchoolNameNormalizer;
use PHPUnit\Framework\TestCase;

class SchoolNameNormalizerTest extends TestCase
{
    public function test_variants_of_the_same_school_name_share_one_key(): void
    {
        $n = new SchoolNameNormalizer;

        $this->assertSame($n->key('SMA NEGERI 3 BANDUNG'), $n->key('SMAN 3 Bandung'));
        $this->assertSame($n->key('SMA NEGERI 3 BANDUNG'), $n->key('SMAN3 Bandung'));
        $this->assertSame($n->key('SD Negeri 01 Sumber'), $n->key('SDN 01 Sumber'));
        $this->assertSame($n->key('MIS Nurul Islamiyah'), $n->key('MI Nurul Islamiyah'));
        $this->assertSame($n->key('SMK Teknik Bakti Persada'), $n->key('SMKS TEKNIK BAKTI PERSADA'));
    }

    public function test_different_schools_get_different_keys(): void
    {
        $n = new SchoolNameNormalizer;

        $this->assertNotSame($n->key('SMAN 3 Bandung'), $n->key('SMAN 1 Bandung'));
        $this->assertNotSame($n->key('SD Harapan'), $n->key('SMP Harapan'));
    }
}
