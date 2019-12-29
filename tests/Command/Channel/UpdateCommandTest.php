<?php

namespace Darkanakin41\VideoBundle\Tests\Command\Channel;

use AppTestBundle\Entity\Channel;
use AppTestBundle\Entity\Video;
use Darkanakin41\VideoBundle\Command\Channel\UpdateCommand;
use Darkanakin41\VideoBundle\Exception\ChannelDoublonException;
use Darkanakin41\VideoBundle\Exception\ChannelNotFoundException;
use Darkanakin41\VideoBundle\Exception\UnknownPlatformException;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Service\ChannelService;
use Darkanakin41\VideoBundle\Tests\AbstractTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateCommandTest extends AbstractTestCase
{
    public function testExecute()
    {
        $channel = $this->createChannel();

        $application = new Application(static::$kernel);
        $application->setAutoExit(false);

        $command = $application->find(UpdateCommand::$defaultName);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $videos = $this->getDoctrine()->getRepository(Video::class)->findBy(['channel' => $channel]);
        $this->assertNotEmpty($videos);
    }

    /**
     * @return Channel
     * @throws ChannelDoublonException
     * @throws ChannelNotFoundException
     * @throws UnknownPlatformException
     */
    private function createChannel()
    {
        $channel = new Channel();
        $channel->setName('darkanakin41');
        $channel->setIdentifier('UCHFq2w-LbRfiemewtlJkoIA');
        $channel->setPlatform(PlatformNomenclature::YOUTUBE);
        $channel->setEnabled(true);

        $url = 'https://www.youtube.com/channel/'.$channel->getIdentifier();
        $this->getChannelService()->create($channel, $url);

        $this->getDoctrine()->getManager()->persist($channel);
        $this->getDoctrine()->getManager()->flush();

        return $channel;
    }

    /**
     * @return ChannelService
     */
    public function getChannelService()
    {
        /** @var ChannelService $service */
        $service = self::$container->get(ChannelService::class);
        return $service;
    }
}
