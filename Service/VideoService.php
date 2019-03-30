<?php

namespace PLejeune\VideoBundle\Service;


use PLejeune\VideoBundle\Entity\Video;
use PLejeune\VideoBundle\Requester\AbstractRequester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class VideoService
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
     * Refresh data from the given list of videos
     *
     * @param string  $platform
     * @param Video[] $videos
     *
     * @throws \Exception
     */
    public function refresh($platform, array $videos)
    {
        $requester = $this->getRequester($platform);
        return $requester->updateVideos($videos);
    }


    /**
     * Retrieve the requester from the pplatform
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
