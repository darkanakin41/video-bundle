<?php


namespace Darkanakin41\VideoBundle\Tests\Service;


use AppTestBundle\Entity\Channel;
use AppTestBundle\Entity\Video;
use Darkanakin41\VideoBundle\Exception\ChannelDoublonException;
use Darkanakin41\VideoBundle\Exception\ChannelNotFoundException;
use Darkanakin41\VideoBundle\Exception\UnknownPlatformException;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Service\ChannelService;
use Darkanakin41\VideoBundle\Tests\AbstractTestCase;

class ChannelServiceTest extends AbstractTestCase
{
    /**
     * @return ChannelService
     */
    public function getService(){
        /** @var ChannelService $service */
        $service = self::$container->get(ChannelService::class);
        return $service;
    }

    /**
     * @return Channel
     */
    private function getChannel(){
        $channel = new Channel();
        $channel->setName('darkanakin41');
        $channel->setIdentifier('UCHFq2w-LbRfiemewtlJkoIA');
        $channel->setPlatform(PlatformNomenclature::YOUTUBE);
        return $channel;
    }

    public function testRetrieveVideos(){
        $channel = $this->getChannel();

        /** @var Video[] $videos */
        $videos = $this->getService()->retrieveVideos($channel);

        $this->assertNotEmpty($videos);
    }

    public function testCreateError(){
        $channel = $this->getChannel();

        $result = $this->getService()->create($channel, '');

        $this->assertFalse($result);
    }

    public function testCreateBasedOnIdentifierInUrl(){
        $channel = $this->getChannel();

        $url = 'https://www.youtube.com/channel/' . $channel->getIdentifier();
        $channel->setName(null);
        $result = $this->getService()->create($channel, $url);

        $this->assertTrue($result);
        $this->assertNotNull($channel->getName());
    }

    public function testCreateBasedOnNameInUrl(){
        $channel = $this->getChannel();

        $url = 'https://www.youtube.com/user/' . $channel->getName();
        $channel->setIdentifier(null);
        $result = $this->getService()->create($channel, $url);

        $this->assertTrue($result);

        $this->assertNotNull($channel->getIdentifier());
    }

    /**
     * @throws ChannelDoublonException
     * @throws ChannelNotFoundException
     * @throws UnknownPlatformException
     */
    public function testCreateChannelNotFoundException(){
        $this->expectException(ChannelNotFoundException::class);
        $channel = $this->getChannel();

        $url = 'https://phpunit.de/manual/6.5/en/code-coverage-analysis.html';
        $channel->setName("a");
        $channel->setIdentifier(null);
        $this->getService()->create($channel, $url);
    }

    public function testCreateChannelDoublonException(){
        $this->expectException(ChannelDoublonException::class);
        $channel = $this->getChannel();

        $url = 'https://www.youtube.com/channel/' . $channel->getIdentifier();
        $result = $this->getService()->create($channel, $url);

        $this->assertTrue($result);

        $this->getDoctrine()->getManager()->persist($channel);
        $this->getDoctrine()->getManager()->flush();

        $channel = $this->getChannel();

        $url = 'https://www.youtube.com/channel/' . $channel->getIdentifier();
        $this->getService()->create($channel, $url);
    }

    public function testCreateUnknownPlatformException(){
        $this->expectException(UnknownPlatformException::class);
        $channel = $this->getChannel();
        $channel->setPlatform(PlatformNomenclature::OTHER);

        $this->getService()->create($channel, $channel->getIdentifier());
    }
}
