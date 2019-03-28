<?php

namespace PLejeune\VideoBundle\Requester;

use PLejeune\ApiBundle\EndPoint\Google\YoutubeEndPoint;
use PLejeune\ApiBundle\Nomenclature\ClientNomenclature;
use PLejeune\ApiBundle\Nomenclature\EndPointNomenclature;
use PLejeune\VideoBundle\Entity\Channel;
use PLejeune\VideoBundle\Entity\Video;

class YoutubeRequester extends AbstractRequester
{

    /**
     * Update the given channel's information
     *
     * @param Channel $channel
     *
     * @throws \Exception
     */
    public function updateChannel(Channel $channel)
    {
        /** @var YoutubeEndPoint $endpoint */
        $endpoint = $this->apiService->getEndPoint(ClientNomenclature::GOOGLE, EndPointNomenclature::YOUTUBE);
        $data = $endpoint->getChannelData($channel->getIdentifier());

        if (count($data['items']) == 0) {
            return;
        }

        $item = $data['items'][0];

        $channel->setUpdated(new \DateTime());
        $channel->setCustomUrl(!empty($item['snippet']['customUrl']) ? $item['snippet']['customUrl'] : null);

        if (isset($item['snippet']['thumbnails']['high'])) {
            $channel->setLogo($item['snippet']['thumbnails']['high']['url']);
        } elseif (isset($item['snippet']['thumbnails']['medium'])) {
            $channel->setLogo($item['snippet']['thumbnails']['medium']['url']);
        } else {
            $channel->setLogo($item['snippet']['thumbnails']['default']['url']);
        }

        $channel->setName($item['snippet']['title']);

        $this->registry->getManager()->persist($channel);
        $this->registry->getManager()->flush();
    }

    /**
     * Retrieve new videos from the given channel
     *
     * @param Channel $channel
     *
     * @return int the number of new videos
     * @throws \Exception
     */
    public function retrieveChannelVideos(Channel $channel)
    {
        /** @var YoutubeEndPoint $endpoint */
        $endpoint = $this->apiService->getEndPoint(ClientNomenclature::GOOGLE, EndPointNomenclature::YOUTUBE);
        $data = $endpoint->getChannelVideos($channel->getIdentifier());

        $created = 0;

        foreach ($data['items'] as $item) {
            $video = $this->registry->getRepository(Video::class)->findOneBy(['provider' => $channel->getProvider(), 'identifier' => $item['id']]);
            if ($video !== null) {
                continue;
            }

            $video = new Video();
            $video->setIdentifier($item['id']['videoId']);
            $video->setChannel($channel);
            $video->setEnabled(true);
            $video->setProvider($channel->getProvider());

            $this->updateVideoData($video, $item);

            $created++;
            $this->registry->getManager()->persist($video);
        }

        $this->registry->getManager()->flush();

        return $created;
    }

    /**
     * Refresh data from the given list of videos
     *
     * @param Video[] $videos
     *
     * @throws \Exception
     */
    public function updateVideos(array $videos)
    {
        /** @var YoutubeEndPoint $endpoint */
        $endpoint = $this->apiService->getEndPoint(ClientNomenclature::GOOGLE, EndPointNomenclature::YOUTUBE);

        $ids = array();
        foreach ($videos as $video) {
            $ids[] = $video->getIdentifier();
        }

        $data = $endpoint->getVideosData($ids);

        foreach ($videos as $video) {
            /** @var array $items */
            $items = array_filter($data['items'], function ($item) use ($video) {
                return $item['id'] === $video->getIdentifier();
            });

            $item = reset($items);

            if($item === false || !isset($item['snippet'])){
                $this->registry->getManager()->remove($video);
                continue;
            }

            $this->updateVideoData($video, $item);
            $this->registry->getManager()->persist($video);
        }

        $this->registry->getManager()->flush();
    }

    private function updateVideoData(Video &$video, array $data){
        $video->setTitle($data['snippet']['title']);
        $video->setChecked(new \DateTime());
        $video->setPublished(new \DateTime($data['snippet']['publishedAt']));

        if (isset($data['snippet']['thumbnails']['high'])) {
            $video->setPreview($data['snippet']['thumbnails']['high']['url']);
        } elseif (isset($data['snippet']['thumbnails']['medium'])) {
            $video->setPreview($data['snippet']['thumbnails']['medium']['url']);
        } else {
            $video->setPreview($data['snippet']['thumbnails']['default']['url']);
        }
    }
}