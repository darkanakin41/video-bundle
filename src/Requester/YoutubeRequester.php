<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Requester;

use Darkanakin41\VideoBundle\Endpoint\YoutubeEndpoint;
use Darkanakin41\VideoBundle\Exception\ChannelDoublonException;
use Darkanakin41\VideoBundle\Exception\ChannelNotFoundException;
use Darkanakin41\VideoBundle\Model\Channel;
use Darkanakin41\VideoBundle\Model\Video;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Service\ChannelService;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Google_Service_YouTube_SearchResult;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class YoutubeRequester extends AbstractRequester
{
    /**
     * @var YoutubeEndpoint
     */
    private $youtubeEndpoint;

    public function __construct(ManagerRegistry $registry, EventDispatcherInterface $eventDispatcher, ChannelService $channelService, ParameterBagInterface $parameterBag, YoutubeEndpoint $youtubeEndpoint)
    {
        parent::__construct($registry, $eventDispatcher, $channelService, $parameterBag);
        $this->youtubeEndpoint = $youtubeEndpoint;
    }

    /**
     * Update the given channel's information.
     *
     * @throws Exception
     */
    public function updateChannel(Channel $channel)
    {
        $data = $this->youtubeEndpoint->getChannelData($channel->getIdentifier());

        if (!isset($data->getItems()[0])) {
            return;
        }

        /** @var \Google_Service_YouTube_Channel $item */
        $item = $data->getItems()[0];

        $channel->setUpdated(new DateTime());
        $channel->setCustomUrl(!empty($item->getSnippet()->getCustomUrl()) ? $item->getSnippet()->getCustomUrl() : null);

        $channel->setLogo($item->getSnippet()->getThumbnails()->getDefault()->getUrl());

        if (null !== $item->getSnippet()->getThumbnails()->getMedium()) {
            $channel->setLogo($item->getSnippet()->getThumbnails()->getMedium()->getUrl());
        }

        if (null !== $item->getSnippet()->getThumbnails()->getHigh()) {
            $channel->setLogo($item->getSnippet()->getThumbnails()->getHigh()->getUrl());
        }

        $channel->setName($item->getSnippet()->getTitle());
    }

    /**
     * Retrieve new videos from the given channel.
     *
     * @return array videos to persist
     *
     * @throws Exception
     */
    public function retrieveChannelVideos(Channel $channel)
    {

        $videos = [];

        if ($channel->getPlatform() !== PlatformNomenclature::YOUTUBE) {
            return $videos;
        }

        $data = $this->youtubeEndpoint->getChannelVideos($channel->getIdentifier());

        foreach ($data->getItems() as $item) {
            /** @var \Google_Service_YouTube_SearchResult $item */
            $video = $this->registry->getRepository($this->getVideoClass())->findOneBy(array('platform' => $channel->getPlatform(), 'identifier' => $item->getId()->getVideoId()));
            if (null !== $video) {
                continue;
            }
            $video = $this->createVideoObject();
            $video->setIdentifier($item->getId()->getVideoId());
            $video->setChannel($channel);
            $video->setEnabled(true);
            $video->setPlatform($channel->getPlatform());

            $this->updateVideoData($video, $item);

            $videos[$video->getPlatform()."-".$video->getIdentifier()] = $video;

            if ('live' === $item->getSnippet()->liveBroadcastContent) {
                $this->triggerIsLiveEvent($video); // @codeCoverageIgnore
            }
        }

        return $videos;
    }

    /**
     * @param Google_Service_YouTube_SearchResult|\Google_Service_YouTube_Video $data
     *
     * @throws Exception
     */
    private function updateVideoData(Video &$video, $data)
    {
        $video->setTitle($data->getSnippet()->getTitle());
        $video->setChecked(new DateTime());
        $video->setPublished(new DateTime($data->getSnippet()->getPublishedAt()));

        $video->setPreview($data->getSnippet()->getThumbnails()->getDefault()->getUrl());

        if ($data->getSnippet()->getThumbnails()->getMedium() !== null) {
            $video->setPreview($data->getSnippet()->getThumbnails()->getMedium()->getUrl());
        }

        if ($data->getSnippet()->getThumbnails()->getHigh() !== null) {
            $video->setPreview($data->getSnippet()->getThumbnails()->getHigh()->getUrl());
        }

        if (null === $video->getChannel()) {
            $channel = null;
            try {
                $channel = $this->createChannelObject();
                $channel->setPlatform(PlatformNomenclature::YOUTUBE);
                $channel->setIdentifier($data->getSnippet()->getChannelId());
                $channel->setEnabled(false);

                $this->channelService->create($channel, $channel->getIdentifier());
            } catch (ChannelDoublonException $e) {
                $channel = $e->getChannel();
            } catch (ChannelNotFoundException $e) {
            }

            $video->setChannel($channel);
        }
    }

    /**
     * Refresh data from the given list of videos.
     *
     * @param Video[] $videos
     *
     * @throws Exception
     */
    public function updateVideos(array $videos)
    {
        $toRemove = [];
        $toUpdate = [];

        $ids = [];
        foreach ($videos as $video) {
            $ids[] = $video->getIdentifier();
        }

        $data = $this->youtubeEndpoint->getVideosData($ids);

        if (count($data->getItems()) === 0) {
            return ["toUpdate" => $toUpdate, "toRemove" => $toRemove];
        }

        foreach ($videos as $video) {
            $videoData = null;
            /** @var \Google_Service_YouTube_Video $item */
            foreach ($data->getItems() as $item) {
                if ($item->getId() === $video->getIdentifier()) {
                    $videoData = $item;
                }
            }

            if (null === $videoData || empty($videoData->getSnippet())) {
                $toRemove[] = $video;
                continue;
            }

            $this->updateVideoData($video, $videoData);
            $toUpdate[] = $video;
        }

        return ["toUpdate" => $toUpdate, "toRemove" => $toRemove];
    }

    /**
     * Retrieve channel's identifier based on his information.
     *
     * @throws Exception
     */
    public function retrieveIdentifier(Channel $channel)
    {
        $data = $this->youtubeEndpoint->getChannelId($channel->getName());

        if (isset($data->getItems()[0])) {
            /** @var \Google_Service_YouTube_Channel $channelData */
            $channelData = $data->getItems()[0];
            $channel->setIdentifier($channelData->getId());
        }
    }
}
