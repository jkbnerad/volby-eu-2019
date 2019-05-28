<?php
declare(strict_types = 1);

namespace App\Command;

use App\Database\Client;
use Dibi\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Strany extends Command
{
    protected static $defaultName = 'app:strany';

    protected function configure()
    {
        $this->setDescription('Vytvoří politické strany, které se zůčastnily voleb.');
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
        $parser = new \App\Strany($db->connection());
        $parsed = $parser->parse();
        $output->writeln(sprintf('Vloženo %d stran.', $parsed));
    }
}
