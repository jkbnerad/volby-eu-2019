<?php
declare(strict_types = 1);

namespace App\Database;


use Dibi\Connection;
use Dibi\Exception;
use RuntimeException;

class Client
{
    private $configFile = __DIR__ . '/../../configs/db.json';

    /**
     * @return Connection
     * @throws Exception
     * @throws \Exception
     */
    public function connection(): Connection
    {
        if (file_exists($this->configFile) === false) {
            throw new RuntimeException('DB: Config file does not exist. Please create file ' . $this->configFile);
        }

        $config = json_decode(file_get_contents($this->configFile), true);
        return new Connection($config);
    }
}
