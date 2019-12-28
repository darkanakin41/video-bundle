<?php

namespace AppTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppTestBundle\Entity\Video
 *
 * @ORM\Table(name="Video")
 * @ORM\Entity(repositoryClass="AppTestBundle\Repository\VideoRepository")
 */
class Video extends \Darkanakin41\VideoBundle\Model\Video
{

    /**
     * @var Channel|null
     * @ORM\ManyToOne(targetEntity="AppTestBundle\Entity\Channel")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $channel;

    /**
     * @inheritDoc
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @inheritDoc
     */
    public function setChannel($channel = null)
    {
        $this->channel = $channel;
    }
}
