<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Requester;

use Darkanakin41\VideoBundle\DependencyInjection\Darkanakin41VideoExtension;
use Darkanakin41\VideoBundle\Model\Channel;
use Darkanakin41\VideoBundle\Model\Video;
use Darkanakin41\VideoBundle\Service\ChannelService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractRequester
{
    /**
     * @var ChannelService
     */
    protected $channelService;
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var ManagerRegistry
     */
    protected $registry;
    /**
     * @var string
     */
    private $videoClass;
    /**
     * @var string
     */
    private $channelClass;

    public function __construct(ManagerRegistry $registry, EventDispatcherInterface $eventDispatcher, ChannelService $channelService, ParameterBagInterface $parameterBag)
    {
        $this->registry = $registry;
        $this->eventDispatcher = $eventDispatcher;
        $this->channelService = $channelService;

        $configuration = $parameterBag->get(Darkanakin41VideoExtension::CONFIG_KEY);
        $this->videoClass = $configuration['video_class'];
        $this->channelClass = $configuration['channel_class'];
    }

    /**
     * @return string
     */
    public function getVideoClass()
    {
        return $this->videoClass;
    }

    /**
     * @return string
     */
    public function getChannelClass()
    {
        return $this->channelClass;
    }

    /**
     * @return Video
     */
    public function createVideoObject()
    {
        $class = $this->getVideoClass();

        return new $class();
    }

    /**
     * @return Channel
     */
    public function createChannelObject()
    {
        $class = $this->getChannelClass();

        return new $class();
    }

    /**
     * Update the given channel's informations.
     */
    abstract public function updateChannel(Channel $channel);

    /**
     * Retrieve new videos from the given channel.
     *
     * @return array videos to persist
     */
    abstract public function retrieveChannelVideos(Channel $channel);

    /**
     * Refresh data from the given list of videos.
     *
     * @param Video[] $videos
     */
    abstract public function updateVideos(array $videos);

    /**
     * Retrieve channel's identifier based on his information.
     *
     * @throws Exception
     */
    abstract public function retrieveIdentifier(Channel $channel);

    /**
     * Trigger an IsLiveEvent.
     *
     * @codeCoverageIgnore
     */
    public function triggerIsLiveEvent(Video $video)
    {
        $eventClass = "\Darkanakin41\StreamBundle\Event\IsLiveEvent";
        if (class_exists($eventClass)) {
            /** @var \Darkanakin41\StreamBundle\Event\IsLiveEvent $event */
            $event = new $eventClass();
            $event->setName($video->getChannel()->getName());
            $event->setLogo($video->getChannel()->getLogo());
            $event->setIdentifier($video->getIdentifier());
            $event->setPlatform($video->getPlatform());

            $this->eventDispatcher->dispatch($event);
        }
    }
}
