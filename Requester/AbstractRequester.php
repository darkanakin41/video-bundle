<?php

namespace PLejeune\VideoBundle\Requester;

use PLejeune\ApiBundle\Service\ApiService;
use PLejeune\VideoBundle\Entity\Channel;
use PLejeune\VideoBundle\Entity\Video;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractRequester
{
    /**
     * @var RegistryInterface
     */
    protected $registry;
    /**
     * @var ApiService
     */
    protected $apiService;

    public function __construct(RegistryInterface $registry, ApiService $apiService)
    {
        $this->registry = $registry;
        $this->apiService = $apiService;
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
}
