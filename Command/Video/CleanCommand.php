<?php

namespace PLejeune\VideoBundle\Command\Video;

use PLejeune\VideoBundle\Entity\Video;
use PLejeune\VideoBundle\Nomenclature\ProviderNomenclature;
use PLejeune\VideoBundle\Repository\VideoRepository;
use PLejeune\VideoBundle\Service\VideoService;
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
        $this->setName('plejeune:video:clean');
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

        foreach (ProviderNomenclature::getAllConstants() as $provider) {
            $progressBar->setMessage(ucfirst($provider));
            $progressBar->display();
            for ($i = 0; $i < self::NB_ITERATIONS; $i++) {
                $videos = $repository->findBy(['provider'=>$provider], ['checked' => 'ASC'], self::PER_ITERATIONS);
                $this->getContainer()->get(VideoService::class)->refresh($provider, $videos);
                $progressBar->advance(self::PER_ITERATIONS);
            }
        }

        $progressBar->finish();
    }

}
