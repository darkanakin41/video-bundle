<?php

namespace PLejeune\VideoBundle\Entity;

/**
 * Channel
 */
class Channel
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $customUrl;

    /**
     * @var string
     */
    private $platform;

    /**
     * @var string|null
     */
    private $logo;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var \DateTime|null
     */
    private $updated;


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
     * Set name.
     *
     * @param string $name
     *
     * @return Channel
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set identifier.
     *
     * @param string $identifier
     *
     * @return Channel
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
     * Set customUrl.
     *
     * @param string $customUrl
     *
     * @return Channel
     */
    public function setCustomUrl($customUrl)
    {
        $this->customUrl = $customUrl;

        return $this;
    }

    /**
     * Get customUrl.
     *
     * @return string
     */
    public function getCustomUrl()
    {
        return $this->customUrl;
    }

    /**
     * Set platform.
     *
     * @param string $platform
     *
     * @return Channel
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

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
     * Set logo.
     *
     * @param string|null $logo
     *
     * @return Channel
     */
    public function setLogo($logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo.
     *
     * @return string|null
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Channel
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
     * Set updated.
     *
     * @param \DateTime|null $updated
     *
     * @return Channel
     */
    public function setUpdated($updated = null)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTime|null
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
