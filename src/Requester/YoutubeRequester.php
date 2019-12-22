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
use Exception;
use Google_Service_YouTube_SearchResult;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class YoutubeRequester extends AbstractRequester
{
    /**
     * @var YoutubeEndpoint
     */
    private $youtubeEndpoint;

    public function __construct(ManagerRegistry $registry, EventDispatcherInterface $eventDispatcher, ChannelService $channelService, ContainerBuilder $containerBuilder, YoutubeEndpoint $youtubeEndpoint)
    {
        parent::__construct($registry, $eventDispatcher, $channelService, $containerBuilder);
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

        if (!$data->getItems()->offsetExists(0)) {
            return;
        }

        /** @var \Google_Service_YouTube_Channel $item */
        $item = $data->getItems()->offsetGet(0);

        $channel->setUpdated(new DateTime());
        $channel->setCustomUrl(!empty($item->getSnippet()->getCustomUrl()) ? $item->getSnippet()->getCustomUrl() : null);

        if ('' !== $item->getSnippet()->getThumbnails()->getHigh()) {
            $channel->setLogo($item->getSnippet()->getThumbnails()->getHigh());
        } elseif ('' !== $item->getSnippet()->getThumbnails()->getMedium()) {
            $channel->setLogo($item->getSnippet()->getThumbnails()->getMedium());
        } else {
            $channel->setLogo($item->getSnippet()->getThumbnails()->getDefault());
        }

        $channel->setName($item->getSnippet()->getTitle());

        $this->registry->getManager()->persist($channel);
        $this->registry->getManager()->flush();
    }

    /**
     * Retrieve new videos from the given channel.
     *
     * @return int the number of new videos
     *
     * @throws Exception
     */
    public function retrieveChannelVideos(Channel $channel)
    {
        $data = $this->youtubeEndpoint->getChannelVideos($channel->getIdentifier());

        $created = 0;

        foreach ($data->getItems() as $item) {
            /** @var \Google_Service_YouTube_SearchResult $item */
            $video = $this->registry->getRepository(Video::class)->findOneBy(array('platform' => $channel->getPlatform(), 'identifier' => $item->getId()));
            if (null !== $video) {
                continue;
            }
            $video = $this->createVideoObject();
            $video->setIdentifier($item->getId()->getVideoId());
            $video->setChannel($channel);
            $video->setEnabled(true);
            $video->setPlatform($channel->getPlatform());

            $this->updateVideoData($video, $item);

            ++$created;
            $this->registry->getManager()->persist($video);

            if ('live' === $item->getSnippet()->liveBroadcastContent) {
                $this->triggerIsLiveEvent($video);
            }
        }

        $this->registry->getManager()->flush();

        return $created;
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
        $ids = array();
        foreach ($videos as $video) {
            $ids[] = $video->getIdentifier();
        }

        $data = $this->youtubeEndpoint->getVideosData($ids);

        if (!isset($data['items'])) {
            return;
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
                $this->registry->getManager()->remove($video);
                continue;
            }

            $this->updateVideoData($video, $videoData);
            $this->registry->getManager()->persist($video);
        }

        $this->registry->getManager()->flush();
    }

    /**
     * Retrieve channel's identifier based on his information.
     *
     * @throws Exception
     */
    public function retrieveIdentifier(Channel $channel)
    {
        $data = $this->youtubeEndpoint->getChannelId($channel->getName());

        if ($data->getItems()->offsetExists(0)) {
            /** @var \Google_Service_YouTube_Channel $channelData */
            $channelData = $data->getItems()->offsetGet(0);
            $channel->setIdentifier($channelData->getId());
        }
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

        if ('' !== $data->getSnippet()->getThumbnails()->getHigh()) {
            $video->setPreview($data->getSnippet()->getThumbnails()->getHigh());
        } elseif ('' !== $data->getSnippet()->getThumbnails()->getMedium()) {
            $video->setPreview($data->getSnippet()->getThumbnails()->getMedium());
        } else {
            $video->setPreview($data->getSnippet()->getThumbnails()->getDefault());
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
}
