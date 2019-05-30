## Výsledky voleb do EU

Z webu http://www.volby.cz jsem stáhl výsledky voleb do Evropského parlamentu.

V adresáři `Data` je soubor `volby-eu-2019.sql` kde jsou v několika tabulkách data s výsledky.

V `Data\CSV` jsou také `CSV` soubory, které jdou běžně otevřít a měly by jít importovat do BI nástrojů nebo do obyčejného Excelu / Google Tabulky. 

## Příklady

**Výsledky ve vybrané obci**

```sql
SELECT 
	o.`nazev` AS `Obec`, 
	s.`nazev` AS `Strana`, 
	ov.`hlasy` AS `Ziskanych hlasu`, 
	ov.`hlasyPct` AS PCT
FROM `Obec` AS o
JOIN `ObecVysledky` AS ov ON ov.`obecId` = o.`obecId`
JOIN `CiselnikNUTS5` AS n5 ON n5.`nuts5` = o.`nuts5`
JOIN `Strana` AS s ON s.`stranaId` = ov.`stranaId`
WHERE n5.`nuts5` = "545678" -- Markvartice, okres Decin
ORDER BY ov.`hlasy` DESC; 
```

| Obec | Strana | Ziskanych hlasu | PCT |
|---|---|---|---|
| Markvartice | ANO 2011 | 34 | 29.82 |
| Markvartice | STAN | 14 | 12.28 |

a tak dále 

**V jaké obci má strana největší podporu**

```sql
SELECT 
	s.`nazev` AS `Strana`, 
	n5.nazev AS Obec, 
	n4.nazev AS Okres, 
	ov.hlasy AS `Ziskane hlasy`, 
	CONCAT(ov.hlasyPct, " %") AS `PCT`
FROM `ObecVysledky` AS ov
JOIN `Strana` AS s ON s.`stranaId` = ov.`stranaId`
JOIN `CiselnikNUTS5` AS n5 ON n5.`nuts5` = ov.`nuts5`
JOIN `CiselnikNUTS4` AS n4 ON n4.`nuts4` = n5.`nuts4`
WHERE ov.`stranaId` = 7 -- STAN + TOP 09
ORDER BY ov.`hlasyPct` DESC
LIMIT 20;
```

| Strana | Obec | Okres | Ziskane hlasy | PCT |
|---|---|---|---|---|
| STAN	| Dolní Studénky	| Šumperk	| 201	| 49.88 %
| STAN	| Žďárek	| Liberec	| 18	| 40.00 %

a tak dále 
