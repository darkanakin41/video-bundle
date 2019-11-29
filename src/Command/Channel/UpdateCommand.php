<?php

namespace Darkanakin41\VideoBundle\Command\Channel;

use Darkanakin41\VideoBundle\Entity\Channel;
use Darkanakin41\VideoBundle\Entity\Video;
use Darkanakin41\StreamBundle\Repository\StreamRepository;
use Darkanakin41\VideoBundle\Nomenclature\ProviderNomenclature;
use Darkanakin41\VideoBundle\Service\ChannelService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('darkanakin41:channel:update');
        $this->setDescription('Recuperation des videos des chaines');
        $this->setHelp('Recuperation des videos des chaines.');
        $this->addArgument('only-active', InputArgument::OPTIONAL, 'Uniquement les activés (true) ou tous (false) ? default : true.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $onlyactive = $input->getArgument('only-active');
        if($onlyactive == "" || $onlyactive == "true"){
            $onlyactive = true;
        }else{
            $onlyactive = false;
        }

        $doctrine = $this->getContainer()->get('doctrine');

        $created = 0;

        /** @var StreamRepository $repository */
        $repository = $doctrine->getRepository(Channel::class);
        if($onlyactive){
            $channels = $repository->findBy(["enabled" => true], ["updated" => "ASC"]);
        }else{
            $channels = $repository->findBy([], ["updated" => "ASC"]);
        }

        $progressBar = new ProgressBar($output, count($channels));
        $progressBar->setFormat('Chaines à traiter : %current%/%max% [%bar%] %message% %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->setMessage(sprintf('(Vidéos créés : %d)', $created));

        $progressBar->start();

        foreach($channels as $channel){
            $this->getContainer()->get(ChannelService::class)->refresh($channel);
            $created += $this->getContainer()->get(ChannelService::class)->retrieveVideos($channel);
            $progressBar->setMessage(sprintf('(Vidéos créés : %d)', $created));
            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln("");
    }

}
