<?php


namespace Darkanakin41\VideoBundle\Tests\Model;


use AppTestBundle\Entity\Channel;
use AppTestBundle\Entity\Video;
use Darkanakin41\CoreBundle\Tests\Model\AbstractEntityTestCase;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use DateTime;

class VideoTest extends AbstractEntityTestCase
{
    /**
     * @return Video
     */
    protected function getEntity()
    {
        return new Video();
    }

    /**
     * @inheritDoc
     */
    public function nullableFieldProvider()
    {
        return [
            ['title', 'toto'],
            ['identifier', 'toto'],
            ['preview', 'toto'],
            ['platform', PlatformNomenclature::YOUTUBE],
            ['published', new DateTime()],
            ['checked', new DateTime()],
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultValueProvider()
    {
        return [
            ['enabled', false, true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function notNullableFieldProvider()
    {
        return [];
    }

    public function testChannel()
    {
        $entity = $this->getEntity();
        $this->assertNull($entity->getChannel());

        $channel = $this->getChannel();
        $channel->setName("toto");
        $entity->setChannel($channel);

        $this->assertNotNull($entity->getChannel());
        $this->assertEquals($channel->getName(), $entity->getChannel()->getName());
    }

    /**
     * @return Channel
     */
    protected function getChannel()
    {
        return $this->getMockForAbstractClass(Channel::class);
    }
}
