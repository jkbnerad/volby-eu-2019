## Výsledky voleb do EU

Z webu http://www.volby.cz jsem stáhl výsledky voleb do Evropského parlamentu.

V adresáři je `Data` je soubor `volby-eu-2019-CZ.sql` kde jsou v několika tabulkách data s výsledky.

V `Data\CSV` jsou také `CSV` soubory, které jdou běžně otevřít a měly by jít importovat do BI nástrojů nebo do obyčejného Excelu / Google Tabulky. 

A dále je tam také velké (100 000 řádků) denormalizovane CSV, které má v sobě data:


| Strana  | Strana zkratka  | Hlasy  |  Hlasy % | Obec  | NUTS 5 | Okres | NUTS 4 | Kraj | Počet obyvatel dané obce |
|---|---|---|---|---| ---| ---| ---| ---| ---|



### Tabulky

#### strana

Strany a jejich souhrné výsledky.

```sql
CREATE TABLE `strana` (
  `stranaId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kodCSU` int(11) unsigned NOT NULL COMMENT 'Kód ČSŮ',
  `nazev` varchar(255) NOT NULL DEFAULT '' COMMENT 'Název strany v rozumné délce',
  `zkratka` varchar(255) NOT NULL DEFAULT '' COMMENT 'Zkratky strany',
  `plnyNazev` text NOT NULL COMMENT 'Plný název strany',
  `pocetStranVKoalici` int(11) unsigned NOT NULL COMMENT 'Počet stran v koalici (1 = bez koalice)',
  `pocetMandatu` int(11) unsigned NOT NULL COMMENT 'Počet získaných mandátů',
  `slozeniStranKodyCSU` json NOT NULL COMMENT 'Strany zastoupené v koalici (kód ČSÚ)',
  PRIMARY KEY (`stranaId`),
  UNIQUE KEY `kodCSU` (`kodCSU`)
)
)
```

#### kraj a krajVysledky

Výsledky za jednotlivé kraje.

```sql
CREATE TABLE `kraj` (
  `krajId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nuts3` varchar(5) NOT NULL DEFAULT '' COMMENT 'NUTS 3',
  `nazev` varchar(255) NOT NULL DEFAULT '',
  `okrsky` int(11) unsigned NOT NULL COMMENT 'Počet okrsků',
  `zpracovano` int(11) unsigned NOT NULL COMMENT 'Počet zpracovaných okrsků',
  `pocetVolicu` int(11) unsigned NOT NULL COMMENT 'Počet voličů',
  `vydaneObalky` int(11) unsigned NOT NULL COMMENT 'Počet vydaných obálek',
  `ucast` decimal(5,2) unsigned NOT NULL COMMENT 'Účast v PCT',
  `odevzdaneObalky` int(11) unsigned NOT NULL COMMENT 'Počet odevzdaných obálek',
  `platneHlasy` int(11) unsigned NOT NULL COMMENT 'Počet platných hlasů',
  PRIMARY KEY (`krajId`),
  UNIQUE KEY `nuts3` (`nuts3`)
)
```

```sql
CREATE TABLE `krajVysledky` (
  `krajVysledkyId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `krajId` int(11) unsigned NOT NULL,
  `stranaId` int(10) unsigned NOT NULL,
  `hlasy` int(11) unsigned NOT NULL COMMENT 'Počet hlasů',
  `hlasyPct` decimal(5,2) unsigned NOT NULL COMMENT 'Počet hlasů PCT',
  PRIMARY KEY (`krajVysledkyId`),
  UNIQUE KEY `krajId_stranaId` (`krajId`,`stranaId`)
)
```

#### obec a obecVysledky

Výsledky za jednotlivé obce.

```
CREATE TABLE `obec` (
  `obecId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `okresId` int(11) DEFAULT NULL,
  `nuts5` varchar(6) NOT NULL DEFAULT '' COMMENT 'Kód NUTS 5',
  `nazev` varchar(255) NOT NULL DEFAULT '',
  `okrsky` int(11) unsigned NOT NULL COMMENT 'Počet okrsků',
  `zpracovano` int(11) unsigned NOT NULL COMMENT 'Počet zpracovaných okrsků',
  `pocetVolicu` int(11) unsigned NOT NULL COMMENT 'Počet voličů',
  `vydaneObalky` int(11) unsigned NOT NULL COMMENT 'Počet vydaných obálek',
  `ucast` decimal(5,2) unsigned NOT NULL COMMENT 'Účast v PCT',
  `odevzdaneObalky` int(11) unsigned NOT NULL COMMENT 'Počet odevzdaných obálek',
  `platneHlasy` int(11) unsigned NOT NULL COMMENT 'Počet platných hlasů',
  PRIMARY KEY (`obecId`),
  UNIQUE KEY `nuts5` (`nuts5`)
) 
```

```sql
CREATE TABLE `obecVysledky` (
  `obecVysledkyId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `obecId` int(11) unsigned NOT NULL,
  `stranaId` int(10) unsigned NOT NULL,
  `hlasy` int(11) unsigned NOT NULL,
  `hlasyPct` decimal(5,2) unsigned NOT NULL,
  PRIMARY KEY (`obecVysledkyId`)
)
```

#### okres a okresVysledky

Výsledky za jednotlivé okresy.

```sql
CREATE TABLE `okres` (
  `okresId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `krajId` int(11) DEFAULT NULL,
  `nuts4` varchar(6) NOT NULL DEFAULT '' COMMENT 'Kód NUTS 4',
  `nazev` varchar(255) NOT NULL DEFAULT '',
  `okrsky` int(11) unsigned NOT NULL COMMENT 'Počet okrsků',
  `zpracovano` int(11) unsigned NOT NULL COMMENT 'Počet zpracovaných okrsků',
  `pocetVolicu` int(11) unsigned NOT NULL COMMENT 'Počet voličů',
  `vydaneObalky` int(11) unsigned NOT NULL COMMENT 'Počet vydaných obálek',
  `ucast` decimal(5,2) unsigned NOT NULL COMMENT 'Účast v PCT',
  `odevzdaneObalky` int(11) unsigned NOT NULL COMMENT 'Počet odevzdaných obálek',
  `platneHlasy` int(11) unsigned NOT NULL COMMENT 'Počet platných hlasů',
  PRIMARY KEY (`okresId`),
  UNIQUE KEY `nuts4` (`nuts4`)
)

```sql
CREATE TABLE `okresVysledky` (
  `okresVysledkyId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `okresId` int(11) unsigned NOT NULL,
  `stranaId` int(10) unsigned NOT NULL,
  `hlasy` int(11) unsigned NOT NULL,
  `hlasyPct` decimal(5,2) unsigned NOT NULL,
  PRIMARY KEY (`okresVysledkyId`),
  UNIQUE KEY `okresId_stranaId` (`okresId`,`stranaId`)
)
```

#### ciselnikNUTS5 a ciselnikNUTSč

Data o okresech a krajích.

Užitečné pro dotazy, kde chcece vzít v úvahu např. počet obyvatel nebo věk.

```sql
CREATE TABLE `ciselnikNUTS5` (
  `nuts5` varchar(6) NOT NULL DEFAULT '',
  `nuts4` varchar(6) NOT NULL DEFAULT '',
  `nazev` varchar(255) NOT NULL DEFAULT '',
  `pocetObyvatel` int(11) unsigned NOT NULL,
  `muzi` int(10) unsigned NOT NULL,
  `zeny` int(10) unsigned NOT NULL,
  `prumernyVek` decimal(5,2) unsigned NOT NULL,
  `prumernyVekMuzi` decimal(5,2) unsigned NOT NULL,
  `prumernyVekZeny` decimal(5,2) unsigned NOT NULL,
  PRIMARY KEY (`nuts5`)
) 
```

```sql
CREATE TABLE `ciselnikNUTS5` (
  `nuts5` varchar(6) NOT NULL DEFAULT '',
  `nuts4` varchar(6) NOT NULL DEFAULT '',
  `nazev` varchar(255) NOT NULL DEFAULT '',
  `pocetObyvatel` int(11) unsigned NOT NULL,
  `muzi` int(10) unsigned NOT NULL,
  `zeny` int(10) unsigned NOT NULL,
  `prumernyVek` decimal(5,2) unsigned NOT NULL,
  `prumernyVekMuzi` decimal(5,2) unsigned NOT NULL,
  `prumernyVekZeny` decimal(5,2) unsigned NOT NULL,
  PRIMARY KEY (`nuts5`)
) 
```


## Příklady

TODO
