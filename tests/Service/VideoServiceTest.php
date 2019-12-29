<?php


namespace Darkanakin41\VideoBundle\Tests\Service;


use AppTestBundle\Entity\Channel;
use AppTestBundle\Entity\Video;
use Darkanakin41\VideoBundle\Exception\UnknownPlatformException;
use Darkanakin41\VideoBundle\Exception\VideoDoublonException;
use Darkanakin41\VideoBundle\Exception\VideoNotFoundException;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Service\VideoService;
use Darkanakin41\VideoBundle\Tests\AbstractTestCase;

class VideoServiceTest extends AbstractTestCase
{
    /**
     * @return VideoService
     */
    public function getService()
    {
        /** @var VideoService $service */
        $service = self::$container->get(VideoService::class);
        return $service;
    }

    /**
     * @return Video
     */
    private function getVideo(){
        $video = new Video();
        $video->setIdentifier('YehUl_xjtqk');
        $video->setPlatform(PlatformNomenclature::YOUTUBE);
        return $video;
    }

    public function testCreateError(){
        $entity = $this->getVideo();

        $result = $this->getService()->create($entity, '');

        $this->assertFalse($result);
    }


    public function testCreateBasedOnIdentifierInUrl(){
        $entity = $this->getVideo();

        $url = 'https://www.youtube.com/watch?v=' . $entity->getIdentifier();
        $result = $this->getService()->create($entity, $url);

        $this->assertTrue($result);
        $this->assertNotNull($entity->getTitle());
    }


    public function testCreateVideoNotFoundException(){

        $this->expectException(VideoNotFoundException::class);

        $entity = $this->getVideo();

        $url = 'https://phpunit.de/manual/6.5/en/code-coverage-analysis.html';
        $entity->setIdentifier(null);
        $result = $this->getService()->create($entity, $url);

        $this->assertTrue($result);
        $this->assertNotNull($entity->getTitle());
    }


    public function testCreateVideoDoublonException(){
        $this->expectException(VideoDoublonException::class);
        $entity = $this->getVideo();

        $url = 'https://www.youtube.com/watch?v=' . $entity->getIdentifier();
        $this->getService()->create($entity, $url);

        $this->getDoctrine()->getManager()->persist($entity->getChannel());
        $this->getDoctrine()->getManager()->persist($entity);

        $this->getDoctrine()->getManager()->flush();
        $entity = $this->getVideo();

        $url = 'https://www.youtube.com/watch?v=' . $entity->getIdentifier();
        $this->getService()->create($entity, $url);
    }


    public function testCreateUnknownPlatformException(){
        $this->expectException(UnknownPlatformException::class);
        $entity = $this->getVideo();

        $url = 'https://phpunit.de/manual/6.5/en/code-coverage-analysis.html';
        $entity->setPlatform(PlatformNomenclature::OTHER);
        $this->getService()->create($entity, $url);

        $this->getDoctrine()->getManager()->persist($entity);

        $this->getDoctrine()->getManager()->flush();
        $entity = $this->getVideo();

        $url = 'https://www.youtube.com/watch?v=' . $entity->getIdentifier();
        $this->getService()->create($entity, $url);
    }

}
