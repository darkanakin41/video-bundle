<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Exception;

use Darkanakin41\VideoBundle\Model\Channel;
use Darkanakin41\VideoBundle\Model\Video;

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
