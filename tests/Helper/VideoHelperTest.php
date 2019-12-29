<?php

namespace Darkanakin41\VideoBundle\Tests\Helper;


use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Helper\VideoHelper;
use PHPUnit\Framework\TestCase;

class VideoHelperTest extends TestCase
{
    /**
     * @dataProvider getPlatformProvider
     *
     * @param string $expectedPlatform the expected platform
     * @param string $url the url to test
     */
    public function testGetPlatform($url, $expectedPlatform)
    {
        $this->assertEquals($expectedPlatform, VideoHelper::getPlatform($url));
    }

    /**
     * @dataProvider getPlatformProvider
     *
     * @param string $expectedPlatform the expected platform
     * @param string $url the url to test
     */
    public function testGetIdentifiant($url, $expectedPlatform, $expectedIdentifier)
    {
        $this->assertEquals($expectedIdentifier, VideoHelper::getIdentifier($url));
    }

    /**
     * Generate tests case in this shape : [url, expectedPlatform, expectedIdentifier]
     * @return array
     */
    public function getPlatformProvider()
    {
        return [
            ['https://www.twitch.tv/zerator/videos', PlatformNomenclature::OTHER, null],
            ['https://www.youtube.com/watch?v=-L2JlFGkFXw', PlatformNomenclature::YOUTUBE, '-L2JlFGkFXw'],
            ['https://www.scoopturn.com/', PlatformNomenclature::OTHER, null],
        ];
    }
}
