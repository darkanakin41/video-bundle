<?php

namespace Darkanakin41\VideoBundle\Command\Video;

use Darkanakin41\VideoBundle\Entity\Video;
use Darkanakin41\VideoBundle\Nomenclature\ProviderNomenclature;
use Darkanakin41\VideoBundle\Repository\VideoRepository;
use Darkanakin41\VideoBundle\Service\VideoService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanCommand extends ContainerAwareCommand
{
    const NB_ITERATIONS = 20;
    const PER_ITERATIONS = 25;

    protected function configure()
    {
        $this->setName('darkanakin41:video:clean');
        $this->setDescription('Mise à jour des vidéos');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');

        /** @var VideoRepository $repository */
        $repository = $doctrine->getRepository(Video::class);

        $progressBar = new ProgressBar($output, self::NB_ITERATIONS * self::PER_ITERATIONS * count(ProviderNomenclature::getAllConstants()));
        $progressBar->setFormat('Vidéos à traiter : %current%/%max% (%message%) [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $progressBar->start();

        foreach (ProviderNomenclature::getAllConstants() as $platform) {
            $progressBar->setMessage(ucfirst($platform));
            $progressBar->display();
            for ($i = 0; $i < self::NB_ITERATIONS; $i++) {
                $videos = $repository->findBy(['platform'=>$platform], ['checked' => 'ASC'], self::PER_ITERATIONS);
                $this->getContainer()->get(VideoService::class)->refresh($platform, $videos);
                $progressBar->advance(self::PER_ITERATIONS);
            }
        }

        $progressBar->finish();
    }

}
