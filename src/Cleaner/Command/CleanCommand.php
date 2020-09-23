<?php


namespace Babymarkt\Composer\Cleaner\Command;

use Babymarkt\Composer\Cleaner\AbstractCommand;
use Babymarkt\Composer\Cleaner\Cleaner;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            'Defines the context to use to clean up files. If no context is given, all contexts are executed.'
        );
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simulate the cleaning process and shows effected files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $context = $input->getArgument('context');

        $cleaner = $this->getCleaner();
        $resultCode = 0;

        if ($input->getOption('dry-run')) {
            $output->writeln('INFO: dry-run mode enabled! No directory or files will be removed.');
            $cleaner->setDryRun(true);
        }

        $cleaner->registerCallback(function ($file, $event) use ($output, &$resultCode) {
            switch ($event) {
                case Cleaner::EVENT_PRE_REMOVE:
                    $output->write(sprintf('Cleaning %s ... ', $file));
                    break;
                case Cleaner::EVENT_REMOVE_SUCCESSFUL:
                    $output->writeln('<info>OK</info>');
                    break;
                case Cleaner::EVENT_REMOVE_FAILED:
                default:
                    $output->writeln('<error>FAILED</error>');
                    $resultCode = 1;
                    break;
            }
        });

        $output->writeln(sprintf('Using cleaning context: <info>%s</info>' . PHP_EOL, $context));
        $cleaner->run($context);

        $output->writeln(PHP_EOL . '<info>Cleaning process finished.</info>');

        return $resultCode;
    }

}