<?php
declare(strict_types = 1);

namespace App\Command;

use App\Database\Client;
use Dibi\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Kraje extends Command
{
    protected static $defaultName = 'app:kraje';

    protected function configure()
    {
        $this->setDescription('Zpracuje výsledky v jednotlivých krajích.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = new Client();
        $parser = new \App\Kraje($db->connection());
        $parser->parse();
    }
}
