<?php


namespace Babymarkt\Composer\Cleaner\Command;


use Babymarkt\Composer\Cleaner\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class CleanCommand extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('babymarkt:cleaner:clean');
        $this->addArgument('context', InputArgument::OPTIONAL,
            'Defines the context to use to clean up files. If no context is given, all contexts are used.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cleaner = $this->getCleaner();
        $cleaner->registerCallback(function ($file, $event) use ($output) {
            $output->writeln('- Removing ' . $file);
        });
        $cleaner->run($input->getArgument('context'));

        $output->writeln('Cleaned up');
    }

}