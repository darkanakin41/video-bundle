<?php

namespace Darkanakin41\VideoBundle\Tests\Command\Video;

use AppTestBundle\Entity\Channel;
use AppTestBundle\Entity\Video;
use Darkanakin41\VideoBundle\Command\Video\CleanCommand;
use Darkanakin41\VideoBundle\Exception\ChannelDoublonException;
use Darkanakin41\VideoBundle\Exception\ChannelNotFoundException;
use Darkanakin41\VideoBundle\Exception\UnknownPlatformException;
use Darkanakin41\VideoBundle\Exception\VideoDoublonException;
use Darkanakin41\VideoBundle\Exception\VideoNotFoundException;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Service\ChannelService;
use Darkanakin41\VideoBundle\Service\VideoService;
use Darkanakin41\VideoBundle\Tests\AbstractTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CleanCommandTest extends AbstractTestCase
{

    /**
     * @group debug
     */
    public function testExecute()
    {
        $videos = $this->createVideos();

        $application = new Application(static::$kernel);
        $application->setAutoExit(false);

        $command = $application->find(CleanCommand::$defaultName);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $toUpdate = $this->getDoctrine()->getRepository(Video::class)->findOneBy(['identifier' => $videos['toUpdate']->getIdentifier()]);
        $this->assertNotNull($toUpdate);

        $toRemove = $this->getDoctrine()->getRepository(Video::class)->findOneBy(['identifier' => $videos['toRemove']->getIdentifier()]);
        $this->assertNull($toRemove);

    }

    /**
     * @return Video[]
     * @throws UnknownPlatformException
     * @throws VideoDoublonException
     * @throws VideoNotFoundException
     */
    private function createVideos()
    {
        $toUpdate = new Video();
        $toUpdate->setIdentifier('YehUl_xjtqk');
        $toUpdate->setPlatform(PlatformNomenclature::YOUTUBE);

        $url = 'https://www.youtube.com/watch?v=' . $toUpdate->getIdentifier();
        $this->getVideoService()->create($toUpdate, $url);

        $this->getDoctrine()->getManager()->persist($toUpdate);
        $this->getDoctrine()->getManager()->persist($toUpdate->getChannel());
        $this->getDoctrine()->getManager()->flush();

        $toRemove = new Video();
        $toRemove->setIdentifier('RAJjsXM8btg');
        $toRemove->setPlatform(PlatformNomenclature::YOUTUBE);

        $url = 'https://www.youtube.com/watch?v=' . $toRemove->getIdentifier();
        $this->getVideoService()->create($toRemove, $url);

        $toRemove->setIdentifier("aaaa");

        $this->getDoctrine()->getManager()->persist($toRemove);
        $this->getDoctrine()->getManager()->flush();

        return ['toUpdate' => $toUpdate, 'toRemove' => $toRemove];
    }

    /**
     * @return VideoService
     */
    public function getVideoService()
    {
        /** @var VideoService $service */
        $service = self::$container->get(VideoService::class);
        return $service;
    }
}
