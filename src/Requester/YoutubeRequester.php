<?php

namespace Darkanakin41\VideoBundle\Requester;

use Exception;
use Darkanakin41\ApiBundle\EndPoint\Google\YoutubeEndPoint;
use Darkanakin41\ApiBundle\Nomenclature\ClientNomenclature;
use Darkanakin41\ApiBundle\Nomenclature\EndPointNomenclature;
use Darkanakin41\VideoBundle\Entity\Channel;
use Darkanakin41\VideoBundle\Entity\Video;
use Darkanakin41\VideoBundle\Exception\ChannelDoublonException;
use Darkanakin41\VideoBundle\Exception\ChannelNotFoundException;
use Darkanakin41\VideoBundle\Nomenclature\ProviderNomenclature;

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

        if (!isset($data['items']) || count($data['items']) == 0) {
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
            $video = $this->registry->getRepository(Video::class)->findOneBy(['platform' => $channel->getPlatform(), 'identifier' => $item['id']]);
            if ($video !== null) {
                continue;
            }

            $video = new Video();
            $video->setIdentifier($item['id']['videoId']);
            $video->setChannel($channel);
            $video->setEnabled(true);
            $video->setPlatform($channel->getPlatform());

            $this->updateVideoData($video, $item);

            $created++;
            $this->registry->getManager()->persist($video);

            if(isset($item['snippet']['liveBroadcastContent']) && $item['snippet']['liveBroadcastContent'] === 'live'){
                $this->triggerIsLiveEvent($video);
            }
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

        if(!isset($data['items'])){
            return;
        }

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

        if($video->getChannel() === null){
            $channel = null;
            try{
                $channel = new Channel();
                $channel->setPlatform(ProviderNomenclature::YOUTUBE);
                $channel->setIdentifier($data['snippet']['channelId']);
                $channel->setEnabled(false);

                $this->channelService->create($channel, $channel->getIdentifier());
            }catch(ChannelDoublonException $e){
                $channel = $e->getChannel();
            } catch (ChannelNotFoundException $e) {}


            $video->setChannel($channel);
        }
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
        /** @var YoutubeEndPoint $endpoint */
        $endpoint = $this->apiService->getEndPoint(ClientNomenclature::GOOGLE, EndPointNomenclature::YOUTUBE);

        $data = $endpoint->getChannelId($channel->getName());

        if (isset($data['pageInfo']) && intval($data['pageInfo']['totalResults']) === 1) {
            $channel->setIdentifier($data['items'][0]['id']);
        }
    }
}
