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
        $data = $this->database->query('SELECT `nuts4` FROM `CiselnikNUTS4`');

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
        $nuts4ToDetails = $this->database->query('SELECT  n4.`nuts4`, n3.`krajId`, n4.`nuts3` FROM `CiselnikNUTS4` AS n4 JOIN `kraj` AS n3 ON n3.`nuts3` = n4.`nuts3`')->fetchAll();
        $nuts4Pairs = [];

        foreach($nuts4ToDetails as $detail) {
            $key = (string) $detail->nuts4;
            $nuts4Pairs[$key] = [
                'krajId' => $detail->krajId,
                'nuts3' => $detail->nuts3
            ];
        }

        foreach (glob($this->dir) as $file) {
            $items = simplexml_load_string(file_get_contents($file));

            foreach ($items->OKRES as $item) {
                ['okresId' => $okresId, 'nuts4' => $nuts4] = $this->parseNuts4($item, $nuts4Pairs);
                $this->parseNuts5($items, $okresId, $nuts4);
            }
        }
    }

    private function parseNuts4($item, array $nuts4Tonuts3): array
    {
        $attributesNuts4 = $item->attributes();

        $attributes = $item->UCAST->attributes();

        $district = [
            'nuts4' => (string) $attributesNuts4['NUTS_OKRES'],
            'krajId' => $nuts4Tonuts3[(string) $attributesNuts4['NUTS_OKRES']]['krajId'],
            'nuts3' => $nuts4Tonuts3[(string) $attributesNuts4['NUTS_OKRES']]['nuts3'],
            'nazev' => (string) $attributesNuts4['NAZ_OKRES'],
            'okrsky' => (int) $attributes['OKRSKY_CELKEM'],
            'zpracovano' => (int) $attributes['OKRSKY_ZPRAC'],
            'pocetVolicu' => (int) $attributes['ZAPSANI_VOLICI'],
            'vydaneObalky' => (int) $attributes['VYDANE_OBALKY'],
            'ucast' => (float) $attributes['UCAST_PROC'],
            'odevzdaneObalky' => (int) $attributes['ODEVZDANE_OBALKY'],
            'platneHlasy' => (int) $attributes['PLATNE_HLASY']
        ];

        $this->database->insert('Okres', $district)->execute();
        $nuts4Id = $this->database->getInsertId();

        foreach ($item->HLASY_STRANA as $vote) {
            $attributes = $vote->attributes();
            $party = [
                'okresId' => $nuts4Id,
                'nuts4' => (string) $attributesNuts4['NUTS_OKRES'],
                'stranaId' => (int) $attributes['ESTRANA'],
                'hlasy' => (int) $attributes['HLASY'],
                'hlasyPct' => (float) $attributes['PROC_HLASU']
            ];
            $this->database->insert('OkresVysledky', $party)->execute();
        }

        return ['okresId' => $nuts4Id, 'nuts4' => (string) $attributesNuts4['NUTS_OKRES']];
    }

    private function parseNuts5($items, int $okresId, string $nuts4): void
    {
        foreach ($items->OBEC as $itemNuts5) {
            $attributesNuts4 = $itemNuts5->attributes();

            $attributes = $itemNuts5->UCAST->attributes();

            $district = [
                'nuts5' => (string) $attributesNuts4['CIS_OBEC'],
                'nuts4' => $nuts4,
                'okresId' => $okresId,
                'nazev' => (string) $attributesNuts4['NAZ_OBEC'],
                'okrsky' => (int) $attributes['OKRSKY_CELKEM'],
                'zpracovano' => (int) $attributes['OKRSKY_ZPRAC'],
                'pocetVolicu' => (int) $attributes['ZAPSANI_VOLICI'],
                'vydaneObalky' => (int) $attributes['VYDANE_OBALKY'],
                'ucast' => (float) $attributes['UCAST_PROC'],
                'odevzdaneObalky' => (int) $attributes['ODEVZDANE_OBALKY'],
                'platneHlasy' => (int) $attributes['PLATNE_HLASY']
            ];

            $this->database->insert('Obec', $district)->execute();
            $nuts5Id = $this->database->getInsertId();

            foreach ($itemNuts5->HLASY_STRANA as $vote) {
                $attributes = $vote->attributes();
                $party = [
                    'obecId' => $nuts5Id,
                    'nuts5' => (string) $attributesNuts4['CIS_OBEC'],
                    'stranaId' => (int) $attributes['ESTRANA'],
                    'hlasy' => (int) $attributes['HLASY'],
                    'hlasyPct' => (float) $attributes['PROC_HLASU']
                ];
                $this->database->insert('ObecVysledky', $party)->execute();
            }

        }
    }

}
