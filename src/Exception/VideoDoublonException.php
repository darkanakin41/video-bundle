<?php

namespace Darkanakin41\VideoBundle\Exception;

use Darkanakin41\VideoBundle\Entity\Channel;
use Darkanakin41\VideoBundle\Entity\Video;

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
