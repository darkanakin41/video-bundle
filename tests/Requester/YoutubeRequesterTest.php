<?php

namespace Darkanakin41\VideoBundle\Tests\Requester;

use AppTestBundle\Entity\Channel;
use AppTestBundle\Entity\Video;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Requester\YoutubeRequester;

class YoutubeRequesterTest extends AbstractRequesterTest
{

    public function getDoctrine(){
        if (self::$container === null) {
            static::createClient();
        }

        $container = self::$container;
        return $container->get("doctrine");
    }
    public function testUpdateChannel()
    {
        $channel = $this->getRequester()->createChannelObject();
        $channel->setIdentifier('UCHFq2w-LbRfiemewtlJkoIA');

        $this->getRequester()->updateChannel($channel);

        $this->assertEquals("Pierre Lejeune", $channel->getName());
        $this->assertNotNull($channel->getLogo());
    }

    /**
     * @return YoutubeRequester
     */
    protected function getRequester()
    {
        if (self::$container === null) {
            static::createClient();
        }

        $container = self::$container;
        /** @var YoutubeRequester $service */
        $service = $container->get(YoutubeRequester::class);
        return $service;
    }

    public function testUpdateChannelError()
    {
        $channel = $this->getRequester()->createChannelObject();
        $channel->setIdentifier('a');

        $this->getRequester()->updateChannel($channel);

        $this->assertNull($channel->getName());
        $this->assertNull($channel->getLogo());
    }

    public function testRetrieveChannelVideos()
    {
        $channel = $this->getRequester()->createChannelObject();
        $channel->setName('darkanakin41');
        $channel->setIdentifier('UCHFq2w-LbRfiemewtlJkoIA');
        $channel->setPlatform(PlatformNomenclature::YOUTUBE);

        $videos = $this->getRequester()->retrieveChannelVideos($channel);

        $this->getDoctrine()->getManager()->persist($channel);
        foreach($videos as $video){
            $this->getDoctrine()->getManager()->persist($video);
        }
        $this->getDoctrine()->getManager()->flush();

        $this->assertNotEmpty($videos);

        $videos = $this->getRequester()->retrieveChannelVideos($channel);

        $this->assertEmpty($videos);
    }

    public function testUpdateVideos()
    {
        $channel = $this->getRequester()->createChannelObject();
        $channel->setName('darkanakin41');
        $channel->setIdentifier('UCHFq2w-LbRfiemewtlJkoIA');
        $channel->setPlatform(PlatformNomenclature::YOUTUBE);

        $videos = $this->getRequester()->retrieveChannelVideos($channel);

        $this->getDoctrine()->getManager()->persist($channel);
        foreach($videos as $video){
            $this->getDoctrine()->getManager()->persist($video);
        }
        $this->getDoctrine()->getManager()->flush();

        $this->assertNotEmpty($videos);

        $videos = $this->getRequester()->updateVideos($videos);

        $this->assertEmpty($videos["toRemove"]);
        $this->assertNotEmpty($videos["toUpdate"]);
    }

    public function testUpdateVideosToRemove()
    {
        $channel = $this->getRequester()->createChannelObject();
        $channel->setName('darkanakin41');
        $channel->setIdentifier('UCHFq2w-LbRfiemewtlJkoIA');
        $channel->setPlatform(PlatformNomenclature::YOUTUBE);

        $videos = $this->getRequester()->retrieveChannelVideos($channel);

        $this->getDoctrine()->getManager()->persist($channel);
        foreach($videos as $video){
            $this->getDoctrine()->getManager()->persist($video);
        }
        $this->getDoctrine()->getManager()->flush();

        $this->assertNotEmpty($videos);

        $video = $videos[array_key_first($videos)];
        $video->setIdentifier("aaaaaaa");

        $videos = $this->getRequester()->updateVideos($videos);

        $this->assertNotEmpty($videos["toRemove"]);
        $this->assertNotEmpty($videos["toUpdate"]);
    }

    public function testUpdateVideosNoResults()
    {
        $channel = $this->getRequester()->createChannelObject();
        $channel->setName('darkanakin41');
        $channel->setIdentifier('UCHFq2w-LbRfiemewtlJkoIA');
        $channel->setPlatform(PlatformNomenclature::YOUTUBE);

        $videos = $this->getRequester()->retrieveChannelVideos($channel);

        $this->getDoctrine()->getManager()->persist($channel);
        foreach($videos as $video){
            $this->getDoctrine()->getManager()->persist($video);
        }
        $this->getDoctrine()->getManager()->flush();

        $this->assertNotEmpty($videos);

        $video = $videos[array_key_first($videos)];
        $video->setIdentifier("aaaaaaa");

        $videos = $this->getRequester()->updateVideos([$video]);

        $this->assertEmpty($videos["toRemove"]);
        $this->assertEmpty($videos["toUpdate"]);
    }

    public function testUpdateVideosNewVideo()
    {
        $video = new Video();
        $video->setIdentifier('YehUl_xjtqk');
        $video->setPlatform(PlatformNomenclature::YOUTUBE);

        $videos = $this->getRequester()->updateVideos([$video]);

        $this->assertEmpty($videos["toRemove"]);
        $this->assertEmpty($videos["toUpdate"]);

        /** @var Video $video */
        $video = $videos["toUpdate"][array_key_first($videos["toUpdate"])];

        $this->assertNotNull($video->getChannel());
    }

    public function testRetrieveChannelVideosWrongPlatform()
    {
        $channel = $this->getRequester()->createChannelObject();

        $videos = $this->getRequester()->retrieveChannelVideos($channel);

        $this->assertEmpty($videos);
    }

    public function testRetrieveIdentifier(){
        $channel = new Channel();
        $channel->setName('darkanakin41');

        $this->getRequester()->retrieveIdentifier($channel);

        $this->assertEquals("UCHFq2w-LbRfiemewtlJkoIA", $channel->getIdentifier());
    }

    protected function setUp(): void
    {
        $this->initDatabase();
    }
}
