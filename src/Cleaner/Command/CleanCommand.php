<?php


namespace Babymarkt\Composer\Cleaner\Command;


use Babymarkt\Composer\Cleaner\Cleaner;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanCommand extends BaseCommand
{

    /**
     * @var Cleaner
     */
    protected $cleaner;

    /**
     * @param Cleaner $cleaner
     */
    public function setCleaner($cleaner)
    {
        $this->cleaner = $cleaner;
    }

    protected function configure()
    {
        $this->setName('babymarkt:cleaner:clean');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Cleaned up');
    }

}