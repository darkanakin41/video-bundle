<?php

namespace PLejeune\VideoBundle\Exception;

use PLejeune\VideoBundle\Entity\Channel;
use PLejeune\VideoBundle\Entity\Video;

class VideoDoublonException extends \Exception
{
    private $video;

    /**
     * @return Channel
     */
    public function getVideo()
    {
        return $this->video;
    }

    public function setVideo(Video $video)
    {
        $this->video = $video;
    }

}
