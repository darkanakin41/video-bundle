<?php

namespace Darkanakin41\VideoBundle\Tests\Nomenclature;

use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use PHPUnit\Framework\TestCase;

class PlatformNomenclatureTest extends TestCase
{
    public function testGetAllConstants()
    {
        $this->assertSame([
            'YOUTUBE' => 'youtube',
            'OTHER' => 'other',
        ], PlatformNomenclature::getAllConstants());
    }
}
