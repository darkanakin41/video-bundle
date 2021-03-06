<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Video.
 *
 * @ORM\MappedSuperclass()
 */
abstract class Video
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="identifier", type="string")
     */
    private $identifier;

    /**
     * @var string
     * @ORM\Column(name="preview", type="string", nullable=true)
     */
    private $preview;

    /**
     * @var string
     * @ORM\Column(name="platform", type="string")
     */
    private $platform;

    /**
     * @var bool
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var \DateTime
     * @ORM\Column(name="published", type="datetime")
     */
    private $published;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="checked", type="datetime", nullable=true)
     */
    private $checked;

    public function __construct()
    {
        $this->enabled = false;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Video
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set identifier.
     *
     * @param string $identifier
     *
     * @return Video
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get preview.
     *
     * @return string
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * Set preview.
     *
     * @param string $preview
     *
     * @return Video
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * Get platform.
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set platform.
     *
     * @param string $platform
     *
     * @return Video
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Video
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get published.
     *
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set published.
     *
     * @param \DateTime $published
     *
     * @return Video
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get checked.
     *
     * @return \DateTime|null
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * Set checked.
     *
     * @param \DateTime|null $checked
     *
     * @return Video
     */
    public function setChecked($checked = null)
    {
        $this->checked = $checked;

        return $this;
    }

    /**
     * Get channel.
     *
     * @return \Darkanakin41\VideoBundle\Model\Channel|null
     */
    abstract public function getChannel();

    /**
     * Set channel.
     *
     * @return Video
     */
    abstract public function setChannel($channel = null);
}
