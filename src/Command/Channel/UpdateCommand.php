<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Command\Channel;

use Darkanakin41\VideoBundle\Model\Channel;
use Darkanakin41\VideoBundle\Service\ChannelService;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    protected static $defaultName = 'darkanakin41:channel:update';
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;
    /**
     * @var ChannelService
     */
    private $channelService;

    public function __construct(ManagerRegistry $managerRegistry, ChannelService $channelService, $name = null)
    {
        parent::__construct($name);

        $this->managerRegistry = $managerRegistry;
        $this->channelService = $channelService;
    }

    protected function configure()
    {
        $this->setDescription('Retrieve videos on channels');
        $this->setHelp('Retrieve videos on channels');
        $this->addArgument('only-active', InputArgument::OPTIONAL, 'Only active one (true) or all (false) ? default : true');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $onlyactive = $input->getArgument('only-active');
        if ('' == $onlyactive || 'true' == $onlyactive) {
            $onlyactive = true;
        } else {
            $onlyactive = false;
        }

        $created = 0;

        /** @var Channel[] $channels */
        $repository = $this->managerRegistry->getRepository(Channel::class);
        if ($onlyactive) {
            $channels = $repository->findBy(array('enabled' => true), array('updated' => 'ASC'));
        } else {
            $channels = $repository->findBy(array(), array('updated' => 'ASC'));
        }

        $progressBar = new ProgressBar($output, count($channels));
        $progressBar->setFormat('Channels to process : %current%/%max% [%bar%] %message% %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $messageTemplate = '(Video created : %d)';
        $progressBar->setMessage(sprintf($messageTemplate, $created));

        $progressBar->start();

        foreach ($channels as $channel) {
            $this->channelService->refresh($channel);
            $created += $this->channelService->retrieveVideos($channel);
            $progressBar->setMessage(sprintf($messageTemplate, $created));
            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln('');
    }
}
