<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Command\Video;

use Darkanakin41\VideoBundle\DependencyInjection\Darkanakin41VideoExtension;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Service\VideoService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CleanCommand extends Command
{
    const NB_ITERATIONS = 20;
    const PER_ITERATIONS = 25;

    public static $defaultName = 'darkanakin41:video:clean';
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;
    /**
     * @var VideoService
     */
    private $videoService;

    /** @var string */
    private $videoClass;

    public function __construct(ManagerRegistry $managerRegistry, VideoService $videoService, ParameterBagInterface $parameterBag, $name = null)
    {
        parent::__construct($name);

        $this->managerRegistry = $managerRegistry;
        $this->videoService = $videoService;

        $configuration = $parameterBag->get(Darkanakin41VideoExtension::CONFIG_KEY);
        $this->videoClass = $configuration['video_class'];
    }

    protected function configure()
    {
        $this->setDescription('Update videos and remove those deleted on platforms');
        $this->setHelp('Update videos and remove those deleted on platforms');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->managerRegistry->getRepository($this->getVideoClass());

        $progressBar = new ProgressBar($output, self::NB_ITERATIONS * self::PER_ITERATIONS * count(PlatformNomenclature::getAllConstants()));
        $progressBar->setFormat('Videos to process : %current%/%max% (%message%) [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $progressBar->start();

        foreach (PlatformNomenclature::getAllConstants() as $platform) {
            $progressBar->setMessage(ucfirst($platform));
            $progressBar->display();
            for ($i = 0; $i < self::NB_ITERATIONS; ++$i) {
                if (PlatformNomenclature::OTHER !== $platform) {
                    $videos = $repository->findBy(array('platform' => $platform), array('checked' => 'ASC'), self::PER_ITERATIONS);
                    $result = $this->videoService->refresh($platform, $videos);

                    foreach ($result['toUpdate'] as $video) {
                        $this->managerRegistry->getManager()->persist($video);
                    }

                    foreach ($result['toRemove'] as $video) {
                        $this->managerRegistry->getManager()->remove($video);
                    }

                    $this->managerRegistry->getManager()->flush();
                }
                $progressBar->advance(self::PER_ITERATIONS);
            }
        }

        $progressBar->finish();
    }

    private function getVideoClass()
    {
        return $this->videoClass;
    }
}
