<?php

namespace PLejeune\VideoBundle\Service;


use Exception;
use PLejeune\VideoBundle\Entity\Channel;
use PLejeune\VideoBundle\Exception\ChannelDoublonException;
use PLejeune\VideoBundle\Exception\ChannelNotFoundException;
use PLejeune\VideoBundle\Helper\ChannelHelper;
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
     * Retrieve new videos from the given channel
     *
     * @param Channel $channel
     *
     * @return int the number of videos created
     * @throws Exception
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
     * @throws Exception
     */
    private function getRequester($platform)
    {
        $classname = sprintf('PLejeune\\VideoBundle\\Requester\\%sRequester', ucfirst(strtolower($platform)));
        if (!class_exists($classname)) throw new Exception('unhandled_platform');
        $object = new $classname($this->container->get('doctrine'), $this->container->get('plejeune.api'), $this->container->get('event_dispatcher'));
        return $object;
    }

    /**
     * Create the Channel Object based on the given URL
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
            return FALSE;
        }

        $data = ChannelHelper::getIdentifier($url);
        $platform = ChannelHelper::getPlatform($url);

        if($platform !== "OTHER"){
            $channel->setPlatform($platform);
        }
        if(!empty($data)){
            $channel->setIdentifier($data["identifier"]);
            $channel->setName($data["name"]);
        }

        if ($channel->getIdentifier() === null) {
            $this->retrieveIdentifier($channel);
        }
        if ($channel->getName() === null) {
            $channel->setName("TO BE UPDATED");
        }

        if ($channel->getIdentifier() === null) {
            throw new ChannelNotFoundException();
        }

        /** @var Channel $exist */
        $exist = $this->container->get('doctrine')->getRepository(Channel::class)->findOneBy(array(
            'identifier' => $channel->getIdentifier(),
            'platform' => $channel->getPlatform(),
        ));

        if ($exist !== null) {
            $exception = new ChannelDoublonException();
            $exception->setChannel($exist);
            throw $exception;
        }

        $this->container->get('doctrine')->getManager()->persist($channel);
        $this->container->get('doctrine')->getManager()->flush();

        $this->refresh($channel);

        return true;
    }

    /**
     * Retrieve channel's identifier based on his information
     *
     * @param Channel $channel
     *
     * @throws Exception
     */
    public function retrieveIdentifier(Channel $channel)
    {
        $requester = $this->getRequester($channel->getPlatform());
        $requester->retrieveIdentifier($channel);
    }

    /**
     * Refresh data from the given channel
     *
     * @param Channel $channel
     *
     * @throws Exception
     */
    public function refresh(Channel $channel)
    {
        $requester = $this->getRequester($channel->getPlatform());
        $requester->updateChannel($channel);
    }

}
