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
     * @param string  $provider
     * @param Video[] $videos
     *
     * @throws \Exception
     */
    public function refresh($provider, array $videos)
    {
        $requester = $this->getRequester($provider);
        return $requester->updateVideos($videos);
    }


    /**
     * Retrieve the requester from the providers
     *
     * @param string $provider
     *
     * @return AbstractRequester
     * @throws \Exception
     */
    private function getRequester($provider)
    {
        $classname = sprintf('PLejeune\\VideoBundle\\Requester\\%sRequester', ucfirst(strtolower($provider)));
        if (!class_exists($classname)) throw new \Exception('unhandled_provider');
        $object = new $classname($this->container->get('doctrine'), $this->container->get('plejeune.api'), $this->container->get('plejeune.stream.twig'));
        return $object;
    }

}
