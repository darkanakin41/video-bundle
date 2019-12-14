<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Command\Video;

use Darkanakin41\VideoBundle\Model\Video;
use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\VideoBundle\Service\VideoService;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanCommand extends Command
{
    const NB_ITERATIONS = 20;
    const PER_ITERATIONS = 25;

    protected static $defaultName = 'darkanakin41:video:clean';
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;
    /**
     * @var VideoService
     */
    private $videoService;

    public function __construct(ManagerRegistry $managerRegistry, VideoService $videoService, $name = null)
    {
        parent::__construct($name);

        $this->managerRegistry = $managerRegistry;
        $this->videoService = $videoService;
    }

    protected function configure()
    {
        $this->setDescription('Update videos and remove those deleted on platforms');
        $this->setHelp('Update videos and remove those deleted on platforms');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->managerRegistry->getRepository(Video::class);

        $progressBar = new ProgressBar($output, self::NB_ITERATIONS * self::PER_ITERATIONS * count(PlatformNomenclature::getAllConstants()));
        $progressBar->setFormat('Videos to process : %current%/%max% (%message%) [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $progressBar->start();

        foreach (PlatformNomenclature::getAllConstants() as $platform) {
            $progressBar->setMessage(ucfirst($platform));
            $progressBar->display();
            for ($i = 0; $i < self::NB_ITERATIONS; ++$i) {
                $videos = $repository->findBy(array('platform' => $platform), array('checked' => 'ASC'), self::PER_ITERATIONS);
                $this->videoService->refresh($platform, $videos);
                $progressBar->advance(self::PER_ITERATIONS);
            }
        }

        $progressBar->finish();
    }
}
