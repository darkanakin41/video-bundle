<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Service;

use Darkanakin41\VideoBundle\Exception\UnknownPlatformException;
use Darkanakin41\VideoBundle\Exception\VideoDoublonException;
use Darkanakin41\VideoBundle\Exception\VideoNotFoundException;
use Darkanakin41\VideoBundle\Helper\VideoHelper;
use Darkanakin41\VideoBundle\Model\Video;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Requester\AbstractRequester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class VideoService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Create the Channel Object based on the given URL.
     *
     * @param $url
     *
     * @return bool
     *
     * @throws VideoNotFoundException
     * @throws VideoDoublonException
     * @throws UnknownPlatformException
     */
    public function create(Video $video, $url)
    {
        if (empty($url)) {
            return false;
        }

        $data = VideoHelper::getIdentifier($url);
        $platform = VideoHelper::getPlatform($url);

        if (null !== $data) {
            $video->setIdentifier($data);
        }
        if (PlatformNomenclature::OTHER !== $platform) {
            $video->setPlatform($platform);
        }
        if (null === $video->getPublished()) {
            $video->setPublished(new \DateTime());
        }
        if (null === $video->getTitle()) {
            $video->setTitle('TO BE UPDATED');
        }

        if (null === $video->getIdentifier()) {
            throw new VideoNotFoundException();
        }

        $requester = $this->getRequester($video->getPlatform());

        /** @var Video $exist */
        $exist = $this->container->get('doctrine')->getRepository($requester->getVideoClass())->findOneBy(array(
            'identifier' => $video->getIdentifier(),
            'platform' => $video->getPlatform(),
        ));

        if (null !== $exist) {
            $exception = new VideoDoublonException();
            $exception->setVideo($exist);
            throw $exception;
        }

        $this->refresh($video->getPlatform(), array($video));

        return true;
    }

    /**
     * Refresh data from the given list of videos.
     *
     * @param string  $platform
     * @param Video[] $videos
     *
     * @throws UnknownPlatformException
     */
    public function refresh($platform, array $videos)
    {
        $requester = $this->getRequester($platform);

        return $requester->updateVideos($videos);
    }

    /**
     * Retrieve the requester from the pplatform.
     *
     * @param string $platform
     *
     * @return AbstractRequester
     *
     * @throws UnknownPlatformException
     */
    private function getRequester($platform)
    {
        $classname = sprintf('Darkanakin41\\VideoBundle\\Requester\\%sRequester', ucfirst(strtolower($platform)));
        if (!class_exists($classname)) {
            throw new UnknownPlatformException();
        }

        /** @var AbstractRequester $object */
        $object = $this->container->get($classname);

        return $object;
    }
}
