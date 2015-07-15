2015-07-12
nach Anleitung im github teil fuer neue module vorgegangen
dann die zusaetzlichen tabellen mit mysql-workbench erzeugt 
nach moodle geschoben und mdl_ als prefix dazu, dann 
tabelle aus mysql erzeugen, source jeweils die tabelle ausgewählt
dest leer gelassen
version.php regelmaessig erhoeht
am nächsten Tag war's weg - mist
2015-07-13
neu - dabei zwischen durch mal cache leeren
hatte primary keys vergessen

2015-07-14
erstellen der tabellen aus vorhandener funktioniert nicht 
verhalten unklar, versuche nochmal neu zu starten nenne modul resop
erst danach ist die Tabelle, die automatischangelegt wird wieder komplett

datetime soll als unix timestamp also int(10) gespeichert werden

beim aktualisieren der db noch ein fehler, in resource_user geht autosequence
nur, wenn primaer-schluessel -> im xml file geaendert

schiebe es mal auf github

2015-07-15 
experimentiere mit den lang-files, sie sollten nach resop/lang/de - andere
module haben sie unter moodleused/... ?

Caching abschalten: https://docs.moodle.org/dev/Developer_Mode
