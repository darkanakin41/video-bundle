<?php

namespace PLejeune\VideoBundle\Requester;

use PLejeune\ApiBundle\Service\ApiService;
use PLejeune\VideoBundle\Entity\Channel;
use PLejeune\VideoBundle\Entity\Video;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractRequester
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var ApiService
     */
    protected $apiService;
    /**
     * @var RegistryInterface
     */
    protected $registry;

    public function __construct(RegistryInterface $registry, ApiService $apiService, EventDispatcherInterface $eventDispatcher)
    {
        $this->registry = $registry;
        $this->apiService = $apiService;
        $this->eventDispatcher = $eventDispatcher;
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
     * Trigger an IsLiveEvent
     *
     * @param Video $video
     */
    public function triggerIsLiveEvent(Video $video){
        $eventClass = "\PLejeune\StreamBundle\Event\IsLiveEvent";
        if(class_exists($eventClass)){
            /** @var \PLejeune\StreamBundle\Event\IsLiveEvent $event */
           $event = new $eventClass();
           $event->setName($video->getChannel()->getName());
           $event->setLogo($video->getChannel()->getLogo());
           $event->setIdentifier($video->getIdentifier());
           $event->setPlatform($video->getPlatform());

           $this->eventDispatcher->dispatch(\PLejeune\StreamBundle\Event\IsLiveEvent::NAME, $event);
        }
    }
}
