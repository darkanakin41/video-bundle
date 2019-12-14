<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Endpoint;

use Google_Client;
use Google_Service_YouTube_Resource_Videos;
use Google_Service_YouTube_VideoListResponse;

class YoutubeEndpoint extends AbstractEndpoint
{
    /** @var Google_Client */
    private $client;

    /**
     * Retrieve all data from the given video ids.
     *
     * @param string[] $channel_id id of channels to update
     *
     * @return Google_Service_YouTube_VideoListResponse
     */
    public function getVideosData(array $channel_id, $maxResults = 50)
    {
        $api = $this->getYoutubeVideosAPI();

        $search = implode(',', $channel_id);

        return $api->listVideos('id,snippet,liveStreamingDetails', array(
            'maxResults' => $maxResults,
            'id' => $search,
        ));
    }

    /**
     * Retrieve the channel id based on his name.
     *
     * @param string $channelName the name of the channel
     *
     * @return \Google_Service_YouTube_ChannelListResponse
     */
    public function getChannelId($channelName)
    {
        $api = $this->getYoutubeChannelsAPI();

        return $api->listChannels('id', array(
            'forUsername' => $channelName,
        ));
    }

    /**
     * Retrieve the channel data.
     *
     * @param string $identifier the identifier of the channel
     *
     * @return \Google_Service_YouTube_ChannelListResponse
     */
    public function getChannelData($identifier)
    {
        $api = $this->getYoutubeChannelsAPI();

        return $api->listChannels('snippet', array(
            'id' => $identifier,
        ));
    }

    /**
     * Retrieve the channel videos.
     *
     * @param string $identifier the identifier of the channel
     *
     * @return \Google_Service_YouTube_SearchListResponse
     */
    public function getChannelVideos($identifier, $maxResults = 50)
    {
        $api = $this->getYoutubeSearchAPI();

        return $api->listSearch('id, snippet', array(
            'maxResults' => $maxResults,
            'order' => 'date',
            'type' => 'video',
            'channelId' => $identifier,
        ));
    }

    /**
     * Retrieve the Youtube Videos API.
     *
     * @return Google_Service_YouTube_Resource_Videos
     */
    protected function getYoutubeVideosAPI()
    {
        $service = new \Google_Service_YouTube($this->client);

        return $service->videos;
    }

    /**
     * Retrieve the Youtube Videos API.
     *
     * @return \Google_Service_YouTube_Resource_Channels
     */
    protected function getYoutubeChannelsAPI()
    {
        $service = new \Google_Service_YouTube($this->client);

        return $service->channels;
    }

    /**
     * Retrieve the Youtube Videos API.
     *
     * @return \Google_Service_YouTube_Resource_Search
     */
    protected function getYoutubeSearchAPI()
    {
        $service = new \Google_Service_YouTube($this->client);

        return $service->search;
    }

    protected function initialize()
    {
        $clientId = $this->getParameterBag()->get('darkanakin41.video.google.clientId');
        $clientSecret = $this->getParameterBag()->get('darkanakin41.video.twitch.clientSecret');
        $this->client = new Google_Client();
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
    }
}
