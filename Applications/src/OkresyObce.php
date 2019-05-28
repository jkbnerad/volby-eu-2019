<?php
declare(strict_types = 1);

namespace App;

use Dibi\Connection;

class OkresyObce
{
    private $dir = __DIR__ . '/../data/xml/okresy/*.xml';
    private $baseUrlNuts4 = 'https://volby.cz/pls/ep2019/vysledky_okres?nuts=%s';
    /**
     * @var Connection
     */
    private $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    /**
     * Downloads XML files with results
     *
     * @throws \Dibi\Exception
     */
    public function downloadData(): void
    {
        $data = $this->database->query('SELECT `nuts4` FROM `ciselnikNUTS4`');

        $baseUrl = $this->baseUrlNuts4;
        $file = __DIR__ . '/../data/xlm/okresy/%s.xml';
        foreach ($data->fetchAll() as $nuts4) {
            $fileName = sprintf($file, $nuts4['nuts4']);
            if (file_exists($fileName) === false) {
                $url = sprintf($baseUrl, $nuts4['nuts4']);
                $data = file_get_contents($url);
                $fileHandler = fopen($fileName, 'w');
                fwrite($fileHandler, $data);
                fclose($fileHandler);
            }
        }
    }


    /**
     * @return void Inserted entities
     */
    public function parse(): void
    {
        $nuts4ToNuts3 = $this->database->query('SELECT  n4.`nuts4`, n3.`krajId` FROM `ciselnikNUTS4` AS n4 JOIN `kraj` AS n3 ON n3.`nuts3` = n4.`nuts3`')->fetchPairs();
        foreach (glob($this->dir) as $file) {
            $items = simplexml_load_string(file_get_contents($file));

            foreach ($items->OKRES as $item) {
                $nuts4Id = $this->parseNuts4($item, $nuts4ToNuts3);
                $this->parseNuts5($items, $nuts4Id);
            }
        }
    }

    private function parseNuts4($item, array $nuts4Tonuts3): int
    {
        $attributesNuts4 = $item->attributes();

        $attributes = $item->UCAST->attributes();

        $district = [
            'nuts4' => (string) $attributesNuts4['NUTS_OKRES'],
            'krajId' => $nuts4Tonuts3[(string) $attributesNuts4['NUTS_OKRES']],
            'nazev' => (string) $attributesNuts4['NAZ_OKRES'],
            'okrsky' => (int) $attributes['OKRSKY_CELKEM'],
            'zpracovano' => (int) $attributes['OKRSKY_ZPRAC'],
            'pocetVolicu' => (int) $attributes['ZAPSANI_VOLICI'],
            'vydaneObalky' => (int) $attributes['VYDANE_OBALKY'],
            'ucast' => (float) $attributes['UCAST_PROC'],
            'odevzdaneObalky' => (int) $attributes['ODEVZDANE_OBALKY'],
            'platneHlasy' => (int) $attributes['PLATNE_HLASY']
        ];

        $this->database->insert('okres', $district)->execute();
        $nuts4Id = $this->database->getInsertId();

        foreach ($item->HLASY_STRANA as $vote) {
            $attributes = $vote->attributes();
            $party = [
                'okresId' => $nuts4Id,
                'stranaId' => (int) $attributes['ESTRANA'],
                'hlasy' => (int) $attributes['HLASY'],
                'hlasyPct' => (float) $attributes['PROC_HLASU']
            ];
            $this->database->insert('okresVysledky', $party)->execute();
        }

        return $nuts4Id;
    }

    private function parseNuts5($items, int $nuts4Id): void
    {
        foreach ($items->OBEC as $itemNuts5) {
            $attributesNuts4 = $itemNuts5->attributes();

            $attributes = $itemNuts5->UCAST->attributes();

            $district = [
                'nuts5' => (string) $attributesNuts4['CIS_OBEC'],
                'okresId' => $nuts4Id,
                'nazev' => (string) $attributesNuts4['NAZ_OBEC'],
                'okrsky' => (int) $attributes['OKRSKY_CELKEM'],
                'zpracovano' => (int) $attributes['OKRSKY_ZPRAC'],
                'pocetVolicu' => (int) $attributes['ZAPSANI_VOLICI'],
                'vydaneObalky' => (int) $attributes['VYDANE_OBALKY'],
                'ucast' => (float) $attributes['UCAST_PROC'],
                'odevzdaneObalky' => (int) $attributes['ODEVZDANE_OBALKY'],
                'platneHlasy' => (int) $attributes['PLATNE_HLASY']
            ];

            $this->database->insert('obec', $district)->execute();
            $nuts5Id = $this->database->getInsertId();

            foreach ($itemNuts5->HLASY_STRANA as $vote) {
                $attributes = $vote->attributes();
                $party = [
                    'obecId' => $nuts5Id,
                    'stranaId' => (int) $attributes['ESTRANA'],
                    'hlasy' => (int) $attributes['HLASY'],
                    'hlasyPct' => (float) $attributes['PROC_HLASU']
                ];
                $this->database->insert('obecVysledky', $party)->execute();
            }

        }
    }

}
