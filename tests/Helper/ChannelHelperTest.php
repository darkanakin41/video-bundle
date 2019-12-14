<?php

namespace Darkanakin41\VideoBundle\Tests\Helper;


use Darkanakin41\VideoBundle\Helper\ChannelHelper;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Helper\VideoHelper;
use PHPUnit\Framework\TestCase;

class ChannelHelperTest extends TestCase
{
    /**
     * @dataProvider getPlatformProvider
     *
     * @param string $expectedPlatform the expected platform
     * @param string $url the url to test
     */
    public function testGetPlatform($url, $expectedPlatform)
    {
        $this->assertEquals($expectedPlatform, ChannelHelper::getPlatform($url));
    }

    /**
     * @dataProvider getPlatformProvider
     *
     * @param string $expectedPlatform the expected platform
     * @param string $url the url to test
     */
    public function testGetIdentifiant($url, $expectedPlatform, $expectedIdentifier)
    {
        $this->assertEquals($expectedIdentifier, ChannelHelper::getIdentifier($url));
    }

    /**
     * Generate tests case in this shape : [url, expectedPlatform, expectedIdentifier]
     * @return array
     */
    public function getPlatformProvider()
    {
        return [
            ['https://www.twitch.tv/zerator/videos', PlatformNomenclature::OTHER, []],
            ['https://www.youtube.com/user/mightycarmods', PlatformNomenclature::YOUTUBE, ['name' => 'mightycarmods', 'identifier' => null]],
            ['https://www.youtube.com/channel/UCgJRL30YS6XFxq9Ga8W2J3A', PlatformNomenclature::YOUTUBE, ['name' => null, 'identifier' => 'UCgJRL30YS6XFxq9Ga8W2J3A']],
            ['https://www.youtube.com/brucegrannec', PlatformNomenclature::YOUTUBE, ['name' => 'brucegrannec', 'identifier' => null]],
            ['https://www.scoopturn.com/', PlatformNomenclature::OTHER, []],
        ];
    }
}
