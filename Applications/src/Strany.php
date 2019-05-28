<?php
declare(strict_types = 1);

namespace App;

use Dibi\Connection;

class Strany
{
    private $file = __DIR__ . '/../data/xml/strany/strany.xml';

    /**
     * @var Connection
     */
    private $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    /**
     * @return int Inserted entities
     * @throws \Dibi\Exception
     */
    public function parse(): int
    {
        $items = simplexml_load_string(file_get_contents($this->file));

        $i = 0;
        foreach ($items as $item) {
            $entity = [
                'stranaId' => (int) $item->ESTRANA,
                'kodCSU' => (int) $item->VSTRANA,
                'nazev' => (string) $item->NAZEVCELK,
                'zkratka' => (string) $item->ZKRATKAE8,
                'plnyNazev' => (string) $item->NAZEVPLNY,
                'pocetStranVKoalici' => (int) $item->POCSTRVKO,
                'pocetMandatu' => (int) $item->POCMANDCR,
                'slozeniStranKodyCSU' => json_encode(explode(',', (string) $item->SLOZENI))
            ];

            $inserted = $this->database->insert('strana', $entity)->execute();

            if ($inserted) {
                $i++;
            }
        }

        return $i;
    }

}
