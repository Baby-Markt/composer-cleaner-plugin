<?php


namespace Babymarkt\Composer\Cleaner\Command;

use Babymarkt\Composer\Cleaner\AbstractCommand;
use Babymarkt\Composer\Cleaner\Cleaner;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class ListCommand extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('babymarkt:cleaner:list');
        $this->addArgument('context', InputArgument::OPTIONAL,
            'Defines the context to use to clean up files. If no context is given, all contexts are executed.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $context = $input->getArgument('context');

        $cleaner = $this->getCleaner();
        $cleaner->setDryRun(true);

        $resultCode = 1;

        $cleaner->registerCallback(function ($file, $event) use ($output, &$resultCode) {
            if ($event === Cleaner::EVENT_PRE_REMOVE) {
                $output->writeln($file);
                $resultCode = 0;
            }
        });
        $cleaner->run($context);

        return $resultCode;
    }

}