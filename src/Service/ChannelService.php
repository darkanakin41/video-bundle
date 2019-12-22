<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Service;

use Darkanakin41\VideoBundle\Exception\ChannelDoublonException;
use Darkanakin41\VideoBundle\Exception\ChannelNotFoundException;
use Darkanakin41\VideoBundle\Helper\ChannelHelper;
use Darkanakin41\VideoBundle\Model\Channel;
use Darkanakin41\VideoBundle\Requester\AbstractRequester;
use Exception;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChannelService
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(ManagerRegistry $managerRegistry, EventDispatcherInterface $eventDispatcher)
    {
        $this->managerRegistry = $managerRegistry;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Retrieve new videos from the given channel.
     *
     * @return int the number of videos created
     *
     * @throws Exception
     */
    public function retrieveVideos(Channel $channel)
    {
        $requester = $this->getRequester($channel->getPlatform());

        return $requester->retrieveChannelVideos($channel);
    }

    /**
     * Create the Channel Object based on the given URL.
     *
     * @param $url
     *
     * @return bool
     *
     * @throws ChannelNotFoundException
     * @throws ChannelDoublonException
     * @throws Exception
     */
    public function create(Channel $channel, $url)
    {
        if (empty($url)) {
            return false;
        }

        $data = ChannelHelper::getIdentifier($url);
        $platform = ChannelHelper::getPlatform($url);

        if ('OTHER' !== $platform) {
            $channel->setPlatform($platform);
        }
        if (!empty($data)) {
            $channel->setIdentifier($data['identifier']);
            $channel->setName($data['name']);
        }

        if (null === $channel->getIdentifier()) {
            $this->retrieveIdentifier($channel);
        }
        if (null === $channel->getName()) {
            $channel->setName('TO BE UPDATED');
        }

        if (null === $channel->getIdentifier()) {
            throw new ChannelNotFoundException();
        }

        /** @var Channel $exist */
        $exist = $this->managerRegistry->getRepository(Channel::class)->findOneBy(array(
            'identifier' => $channel->getIdentifier(),
            'platform' => $channel->getPlatform(),
        ));

        if (null !== $exist) {
            $exception = new ChannelDoublonException();
            $exception->setChannel($exist);
            throw $exception;
        }

        $this->managerRegistry->getManager()->persist($channel);
        $this->managerRegistry->getManager()->flush();

        $this->refresh($channel);

        return true;
    }

    /**
     * Retrieve channel's identifier based on his information.
     *
     * @throws Exception
     */
    public function retrieveIdentifier(Channel $channel)
    {
        $requester = $this->getRequester($channel->getPlatform());
        $requester->retrieveIdentifier($channel);
    }

    /**
     * Refresh data from the given channel.
     *
     * @throws Exception
     */
    public function refresh(Channel $channel)
    {
        $requester = $this->getRequester($channel->getPlatform());
        $requester->updateChannel($channel);
    }

    /**
     * Retrieve the requester from the platform.
     *
     * @param string $platform
     *
     * @return AbstractRequester
     *
     * @throws Exception
     */
    private function getRequester($platform)
    {
        $classname = sprintf('Darkanakin41\\VideoBundle\\Requester\\%sRequester', ucfirst(strtolower($platform)));
        if (!class_exists($classname)) {
            throw new Exception('unhandled_platform');
        }

        return new $classname($this->managerRegistry, $this->eventDispatcher);
    }
}
