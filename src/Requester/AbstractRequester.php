<?php

namespace Darkanakin41\VideoBundle\Requester;

use Exception;
use Darkanakin41\ApiBundle\Service\ApiService;
use Darkanakin41\VideoBundle\Entity\Channel;
use Darkanakin41\VideoBundle\Entity\Video;
use Darkanakin41\VideoBundle\Service\ChannelService;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractRequester
{
    /**
     * @var ApiService
     */
    protected $apiService;
    /**
     * @var ChannelService
     */
    protected $channelService;
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var RegistryInterface
     */
    protected $registry;

    public function __construct(RegistryInterface $registry, ApiService $apiService, EventDispatcherInterface $eventDispatcher, ChannelService $channelService)
    {
        $this->registry = $registry;
        $this->apiService = $apiService;
        $this->eventDispatcher = $eventDispatcher;
        $this->channelService = $channelService;
    }

    /**
     * Update the given channel's informations
     *
     * @param Channel $channel
     */
    abstract public function updateChannel(Channel $channel);

    /**
     * Retrieve new videos from the given channel
     *
     * @param Channel $channel
     *
     * @return int the number of new videos
     */
    abstract public function retrieveChannelVideos(Channel $channel);

    /**
     * Refresh data from the given list of videos
     *
     * @param Video[] $videos
     */
    abstract public function updateVideos(array $videos);

    /**
     * Retrieve channel's identifier based on his information
     *
     * @param Channel $channel
     *
     * @throws Exception
     */
    abstract public function retrieveIdentifier(Channel $channel);

    /**
     * Trigger an IsLiveEvent
     *
     * @param Video $video
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

            $this->eventDispatcher->dispatch(\Darkanakin41\StreamBundle\Event\IsLiveEvent::NAME, $event);
        }
    }
}
