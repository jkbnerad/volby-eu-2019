<?php
declare(strict_types = 1);

namespace App;

use Dibi\Connection;

class Kraje
{
    private $file = __DIR__ . '/../data/xml/kraje/kraje.xml';

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
            $attributesDistrict = $item->attributes();
            $attributes = $item->UCAST->attributes();

            $district = [
                'nuts3' => (string) $attributesDistrict['NUTS_KRAJ'],
                'nazev' => (string) $attributesDistrict['NAZ_KRAJ'],
                'okrsky' => (int) $attributes['OKRSKY_CELKEM'],
                'zpracovano' => (int) $attributes['OKRSKY_ZPRAC'],
                'pocetVolicu' => (int) $attributes['ZAPSANI_VOLICI'],
                'vydaneObalky' => (int) $attributes['VYDANE_OBALKY'],
                'ucast' => (float) $attributes['UCAST_PROC'],
                'odevzdaneObalky' => (int) $attributes['ODEVZDANE_OBALKY'],
                'platneHlasy' => (int) $attributes['PLATNE_HLASY']
            ];

            $this->database->insert('kraj', $district)->execute();
            $districtId = $this->database->getInsertId();

            foreach ($item->HLASY_STRANA as $vote) {
                $attributes = $vote->attributes();
                $party = [
                    'krajId' => $districtId,
                    'stranaId' => (int) $attributes['ESTRANA'],
                    'hlasy' => (int) $attributes['HLASY'],
                    'hlasyPct' => (float) $attributes['PROC_HLASU']
                ];
                $this->database->insert('krajVysledky', $party)->execute();
            }

            $i++;
        }

        return $i;
    }

}
