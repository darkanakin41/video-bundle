<?php

namespace PLejeune\VideoBundle\Exception;

use PLejeune\VideoBundle\Entity\Channel;

class ChannelDoublonException extends \Exception
{
    private $channel;

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    public function setChannel(Channel $channel)
    {
        $this->channel = $channel;
    }

}
