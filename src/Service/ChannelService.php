<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Service;

use Darkanakin41\VideoBundle\Exception\ChannelDoublonException;
use Darkanakin41\VideoBundle\Exception\ChannelNotFoundException;
use Darkanakin41\VideoBundle\Exception\UnknownPlatformException;
use Darkanakin41\VideoBundle\Helper\ChannelHelper;
use Darkanakin41\VideoBundle\Model\Channel;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Requester\AbstractRequester;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
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

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ManagerRegistry $managerRegistry, EventDispatcherInterface $eventDispatcher, ContainerInterface $container)
    {
        $this->managerRegistry = $managerRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->container = $container;
    }

    /**
     * Retrieve new videos from the given channel.
     *
     * @return array videos to persist
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
     * @throws UnknownPlatformException
     */
    public function create(Channel $channel, $url)
    {
        if (empty($url)) {
            return false;
        }

        $data = ChannelHelper::getIdentifier($url);
        $platform = ChannelHelper::getPlatform($url);

        if (PlatformNomenclature::OTHER !== $platform) {
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

        $requester = $this->getRequester($channel->getPlatform());

        /** @var Channel $exist */
        $exist = $this->managerRegistry->getRepository($requester->getChannelClass())->findOneBy(array(
            'identifier' => $channel->getIdentifier(),
            'platform' => $channel->getPlatform(),
        ));

        if (null !== $exist) {
            $exception = new ChannelDoublonException();
            $exception->setChannel($exist);
            throw $exception;
        }

        $this->refresh($channel);

        return true;
    }

    /**
     * Retrieve channel's identifier based on his information.
     *
     * @throws UnknownPlatformException
     */
    public function retrieveIdentifier(Channel $channel)
    {
        $requester = $this->getRequester($channel->getPlatform());
        $requester->retrieveIdentifier($channel);
    }

    /**
     * Refresh data from the given channel.
     *
     * @throws UnknownPlatformException
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
     * @throws UnknownPlatformException
     */
    private function getRequester($platform)
    {
        $classname = sprintf('Darkanakin41\\VideoBundle\\Requester\\%sRequester', ucfirst(strtolower($platform)));
        if (!class_exists($classname)) {
            throw new UnknownPlatformException();
        }

        /** @var AbstractRequester $requester */
        $requester = $this->container->get($classname);

        return $requester;
    }
}
