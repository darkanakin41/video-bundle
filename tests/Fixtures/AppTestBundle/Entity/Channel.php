<?php

namespace AppTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppTestBundle\Entity\Channel
 * @ORM\Table(name="channel")
 * @ORM\Entity(repositoryClass="AppTestBundle\Repository\ChannelRepository")
 */
class Channel extends \Darkanakin41\VideoBundle\Model\Channel
{
}
