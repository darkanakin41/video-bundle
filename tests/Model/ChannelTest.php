<?php


namespace Darkanakin41\VideoBundle\Tests\Model;


use Darkanakin41\CoreBundle\Tests\Model\AbstractEntityTestCase;
use Darkanakin41\VideoBundle\Model\Channel;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use DateTime;

class ChannelTest extends AbstractEntityTestCase
{
    /**
     * @return Channel
     */
    protected function getEntity()
    {
        return $this->getMockForAbstractClass(Channel::class);
    }

    public function nullableFieldProvider()
    {
        return [
            ['name', 'toto'],
            ['identifier', 'toto'],
            ['customUrl', 'toto'],
            ['platform', PlatformNomenclature::YOUTUBE],
            ['logo', 'toto'],
            ['updated', new DateTime()],
        ];
    }

    public function defaultValueProvider()
    {
        return [
            ['enabled', false, true]
        ];
    }
}
