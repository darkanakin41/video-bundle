<?php

namespace PLejeune\VideoBundle\Service;


use PLejeune\VideoBundle\Entity\Channel;
use PLejeune\VideoBundle\Requester\AbstractRequester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChannelService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Refresh data from the given channel
     *
     * @param Channel $channel
     *
     * @throws \Exception
     */
    public function refresh(Channel $channel)
    {
        $requester = $this->getRequester($channel->getPlatform());
        $requester->updateChannel($channel);
    }

    /**
     * Retrieve new videos from the given channel
     *
     * @param Channel $channel
     *
     * @return int the number of videos created
     * @throws \Exception
     */
    public function retrieveVideos(Channel $channel)
    {
        $requester = $this->getRequester($channel->getPlatform());
        return $requester->retrieveChannelVideos($channel);
    }

    /**
     * Retrieve the requester from the platform
     *
     * @param string $platform
     *
     * @return AbstractRequester
     * @throws \Exception
     */
    private function getRequester($platform)
    {
        $classname = sprintf('PLejeune\\VideoBundle\\Requester\\%sRequester', ucfirst(strtolower($platform)));
        if (!class_exists($classname)) throw new \Exception('unhandled_platform');
        $object = new $classname($this->container->get('doctrine'), $this->container->get('plejeune.api'), $this->container->get('event_dispatcher'));
        return $object;
    }

}
