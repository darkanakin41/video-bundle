<?php

namespace PLejeune\VideoBundle\Entity;

/**
 * Video
 */
class Video
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $preview;

    /**
     * @var string
     */
    private $provider;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var \DateTime
     */
    private $published;

    /**
     * @var \DateTime|null
     */
    private $checked;

    /**
     * @var \PLejeune\VideoBundle\Entity\Channel
     */
    private $channel;


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
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Get identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
     * Get preview.
     *
     * @return string
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * Set provider.
     *
     * @param string $provider
     *
     * @return Video
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider.
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
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
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
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
     * Get published.
     *
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->published;
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
     * Get checked.
     *
     * @return \DateTime|null
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * Set channel.
     *
     * @param \PLejeune\VideoBundle\Entity\Channel|null $channel
     *
     * @return Video
     */
    public function setChannel(\PLejeune\VideoBundle\Entity\Channel $channel = null)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel.
     *
     * @return \PLejeune\VideoBundle\Entity\Channel|null
     */
    public function getChannel()
    {
        return $this->channel;
    }
}
