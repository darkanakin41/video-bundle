<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Exception;

use Darkanakin41\VideoBundle\Model\Channel;

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
