<?php

namespace Darkanakin41\VideoBundle\Service;


use Exception;
use Darkanakin41\VideoBundle\Entity\Video;
use Darkanakin41\VideoBundle\Exception\ChannelDoublonException;
use Darkanakin41\VideoBundle\Exception\VideoDoublonException;
use Darkanakin41\VideoBundle\Exception\VideoNotFoundException;
use Darkanakin41\VideoBundle\Helper\VideoHelper;
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
     * Create the Channel Object based on the given URL
     *
     * @param $url
     *
     * @return bool
     *
     * @throws VideoNotFoundException
     * @throws VideoDoublonException
     * @throws Exception
     */
    public function create(Video $video, $url)
    {
        if (empty($url)) {
            return FALSE;
        }

        $data = VideoHelper::getIdentifier($url);
        $platform = VideoHelper::getPlatform($url);

        $video->setPlatform($platform);
        $video->setIdentifier($data);
        if($video->getPublished() === null){
            $video->setPublished(new \DateTime());
        }
        if($video->getTitle() === null){
            $video->setTitle("TO BE UPDATED");
        }

        if ($video->getIdentifier() === null) {
            throw new VideoNotFoundException();
        }

        /** @var Video $exist */
        $exist = $this->container->get('doctrine')->getRepository(Video::class)->findOneBy(array(
            'identifier' => $video->getIdentifier(),
            'platform' => $video->getPlatform(),
        ));

        if ($exist !== null) {
            $exception = new VideoDoublonException();
            $exception->setVideo($exist);
            throw $exception;
        }

        $this->container->get('doctrine')->getManager()->persist($video);
        $this->container->get('doctrine')->getManager()->flush();

        $this->refresh($video->getPlatform(), [$video]);

        return true;
    }

    /**
     * Refresh data from the given list of videos
     *
     * @param string  $platform
     * @param Video[] $videos
     *
     * @throws \Exception
     */
    public function refresh($platform, array $videos)
    {
        $requester = $this->getRequester($platform);
        return $requester->updateVideos($videos);
    }

    /**
     * Retrieve the requester from the pplatform
     *
     * @param string $platform
     *
     * @return AbstractRequester
     * @throws \Exception
     */
    private function getRequester($platform)
    {
        $classname = sprintf('Darkanakin41\\VideoBundle\\Requester\\%sRequester', ucfirst(strtolower($platform)));
        if (!class_exists($classname)) throw new \Exception('unhandled_platform');
        $object = new $classname(
            $this->container->get('doctrine'),
            $this->container->get('darkanakin41.api'),
            $this->container->get('event_dispatcher'),
            $this->container->get(ChannelService::class)
        );
        return $object;
    }

}
